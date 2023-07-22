<?php

namespace App\Service;

use App\Entity\AccountType;
use App\Entity\Connector;
use App\Exception\SynchronizationException;
use App\Model\PowensApi\BankAccount;
use App\Model\PowensApi\Connection;
use App\Model\PowensApi\Document;
use App\Model\PowensApi\Investment;
use App\Model\PowensApi\Subscription;
use App\Model\PowensApi\Transaction;
use App\Model\PermanentUserAccessToken;
use App\Model\TemporaryCode;
use App\Model\UserAccessToken;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BudgetInsightApiService
{
    const USER_ACCESS_TOKEN_ENDPOINT = '/auth/init';
    const TEMPORARY_CODE_ENDPOINT = '/auth/token/code';
    const PERMANENT_USER_ACCESS_TOKEN_ENDPOINT = '/auth/token/access';

    const LIST_BANK_ACCOUNTS_ENDPOINT = '/users/me/accounts';
    const LIST_CONNECTIONS_ENDPOINT = '/users/me/connections';
    const LIST_DOCUMENTS_ENDPOINT = '/users/me/documents';
    const LIST_INVESTMENTS_ENDPOINT = '/users/me/investments';
    const LIST_SUBSCRIPTIONS_ENDPOINT = '/users/me/subscriptions';

    const LIST_CONNECTORS_ENDPOINT = '/connectors';
    const LIST_ACCOUNT_TYPES_ENDPOINT = '/account_types';

    private array $options = [];

    public function __construct(
        private readonly HttpClientInterface   $budgetInsightClient,
        private readonly SerializerInterface   $serializer,
        private readonly Security              $security,
        private readonly ParameterBagInterface $parameters
    )
    {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function deleteConnection(int $id): int
    {
        $this->useBearerToken();

        $response = $this->budgetInsightClient->request(Request::METHOD_DELETE, "/users/me/connections/$id", $this->options);

        return $response->getStatusCode();
    }

    /**
     * This endpoint generates a new access token related to a new user.
     * @return UserAccessToken
     */
    public function generateNewUserAccessToken(): UserAccessToken
    {
        $data = $this->request('POST', self::USER_ACCESS_TOKEN_ENDPOINT);

        return $this->serializer->deserialize($data, UserAccessToken::class, 'json');
    }

    /**
     * This endpoint generates a new temporary code for the current user.
     */
    public function generateTemporaryCode(bool $useBearerToken = false): TemporaryCode
    {
        if ($useBearerToken) {
            $this->useBearerToken();
        } else {
            $this->options['auth_bearer'] = $this->generateNewUserAccessToken()->auth_token;
        }

        $data = $this->request('GET', self::TEMPORARY_CODE_ENDPOINT);

        return $this->serializer->deserialize($data, TemporaryCode::class, 'json');
    }

    /**
     * This endpoint uses the received temporary token to generate a permanent user access token.
     * @param string $code
     * @return PermanentUserAccessToken
     */
    public function generatePermanentUserAccessToken(string $code): PermanentUserAccessToken
    {
        $this->options['body'] = [
            'client_id' => $this->parameters->get('client_id'),
            'client_secret' => $this->parameters->get('client_secret'),
            'code' => $code
        ];

        $data = $this->request('POST', self::PERMANENT_USER_ACCESS_TOKEN_ENDPOINT);

        return $this->serializer->deserialize($data, PermanentUserAccessToken::class, 'json');
    }

    public function getBankAccount(int $id): ?BankAccount
    {
        $this->useBearerToken();

        $baseUrl = $this->parameters->get('base_url');

        $data = $this->request(Request::METHOD_GET, "$baseUrl/users/me/accounts/$id");

        if (empty($data)) {
            return null;
        }

        return $this->serializer->deserialize($data, BankAccount::class, 'json');
    }

    /**
     * @return AccountType[]
     */
    public function listAccountTypes(): array
    {
        $data = json_decode($this->request('GET', self::LIST_ACCOUNT_TYPES_ENDPOINT), true);

        return $this->serializer->deserialize(json_encode($data['accounttypes']), 'App\Entity\AccountType[]', 'json');
    }

    /**
     * @return BankAccount[]
     */
    public function listBankAccounts(?array $types = null): array
    {
        $response = [];

        $this->useBearerToken();

        $data = json_decode($this->request('GET', self::LIST_BANK_ACCOUNTS_ENDPOINT), true);

        /** @var BankAccount[] $accounts */
        $accounts = $this->serializer->deserialize(json_encode($data['accounts']), 'App\Model\PowensApi\BankAccount[]', 'json');

        if (!$types) {
            return $accounts;
        }

        foreach ($accounts as $account) {
            if(in_array($account->type, $types)) {
                $response[] = $account;
            }
        }

        return $response;
    }

    /**
     * @return Connection[]
     */
    public function listConnections(): array
    {
        $this->useBearerToken();

        $data = json_decode($this->request('GET', self::LIST_CONNECTIONS_ENDPOINT), true);

        return $this->serializer->deserialize(json_encode($data['connections']), 'App\Model\PowensApi\Connection[]', 'json');
    }

    /**
     * @return Connector[]
     */
    public function listConnectors(): array
    {
        $data = json_decode($this->request('GET', self::LIST_CONNECTORS_ENDPOINT), true);

        return $this->deserialize($data['connectors'], 'App\Entity\Connector[]', true);
    }

    /**
     * @return Document[]
     */
    public function listDocuments(): array
    {
        $data = json_decode($this->request('GET', self::LIST_DOCUMENTS_ENDPOINT), true);

        return $this->deserialize($data['documents'], 'App\Model\PowensApi\Document[]', true);
    }

    public function listDocumentTypes(): array
    {
        $data = json_decode($this->request('GET', self::LIST_CONNECTORS_ENDPOINT), true);

        return $this->deserialize($data['documenttypes'], 'App\Model\PowensApi\DocumentType[]', true);
    }

    /**
     * @return Investment[]
     */
    public function listInvestments(): array
    {
        $this->useBearerToken();

        $data = json_decode($this->request('GET', self::LIST_INVESTMENTS_ENDPOINT), true);

        return $this->serializer->deserialize(json_encode($data['investments']), 'App\Model\PowensApi\Investment[]', 'json');
    }

    /**
     * @return Investment[]
     */
    public function listInvestmentsByAccount(int $id): array
    {
        $this->useBearerToken();

        $baseUrl = $this->parameters->get('base_url');

        $data = json_decode($this->request('GET', "$baseUrl/users/me/accounts/$id/investments"), true);

        return $this->serializer->deserialize(json_encode($data['investments']), 'App\Model\PowensApi\Investment[]', 'json');
    }

    /**
     * @return Subscription[]
     */
    public function listSubscriptions(): array
    {
        $this->useBearerToken();

        $this->options['query'] = [
            'expand' => 'connection'
        ];

        $data = json_decode($this->request('GET', self::LIST_SUBSCRIPTIONS_ENDPOINT), true);

        return $this->deserialize($data['subscriptions'], 'App\Model\PowensApi\Subscription[]', true);
    }

    /**
     * @return Transaction[]
     */
    public function listTransactions(?int $accountId = null, int $offset = 0, ?int $limit = 10, ?\DateTime $maxDate = null): array
    {
        $this->useBearerToken();

        $url = (null === $accountId) ? "users/me/transactions" : "users/me/accounts/$accountId/transactions";

        $this->options['query'] = [
            'offset' => $offset,
            'limit' => $limit,
            'max_date' => $maxDate?->format('Y-m-d')
        ];

        $data = json_decode($this->request('GET', $url), true);

        return $this->serializer->deserialize(json_encode($data['transactions']), 'App\Model\PowensApi\Transaction[]', 'json');
    }

    public function getDocumentFile(int $idDocument, string $webid): string
    {
        $this->useBearerToken();

        $baseUrl = $this->parameters->get('base_url');

        $url = "$baseUrl/users/me/documents/$idDocument/file/$webid.png";

        return $this->request(Request::METHOD_GET, $url);
    }

    public function getDocumentThumbnail(int $idDocument, string $webid): string
    {
        $this->useBearerToken();

        $baseUrl = $this->parameters->get('base_url');

        $url = "$baseUrl/users/me/documents/$idDocument/thumbnail/$webid.png";

        return $this->request(Request::METHOD_GET, $url);
    }

    public function getWebviewUrl(string $token, string $type = 'connect', string $connectorCapabilities = "bank"): string
    {
        $baseUrl = $this->parameters->get('base_url');

        $data = [
            'client_id' => $this->parameters->get('client_id'),
            'redirect_uri' => $this->parameters->get('redirect_uri'),
            'connector_capabilities' => $connectorCapabilities
        ];

        if ('manage' === $type) {
            $data['code'] = $this->generateTemporaryCode($token)->code;
        }

        $query = http_build_query($data);

        return "$baseUrl/auth/webview/manage?$query";
    }

    public function updateConnection(int $id): Connection
    {
        $baseUrl = $this->parameters->get('base_url');

        $this->useBearerToken();

        $this->options['query'] = [
            'background' => true
        ];

        $data = $this->request(Request::METHOD_POST, "$baseUrl/users/me/connections/$id");

        return $this->serializer->deserialize($data, Connection::class, 'json');
    }

    /**
     * @throws SynchronizationException
     */
    public function syncConnection(int $id): Connection
    {
        $baseUrl = $this->parameters->get('base_url');

        $this->useBearerToken();

        $this->options['query'] = [
            'psu_requested' => false
        ];

        $data = $this->request(Request::METHOD_PUT, "$baseUrl/users/me/connections/$id");

        return $this->deserialize($data, Connection::class);
    }

    private function deserialize(mixed $data, string $type, bool $useJsonEncode = false, string $format = 'json'): mixed
    {
        if ($useJsonEncode) {
            $data = json_encode($data);
        }

        return $this->serializer->deserialize($data, $type, $format);
    }

    private function useBearerToken(): void
    {
        $bearerToken = $this->security->getUser()->getBearerToken();

        $this->options['auth_bearer'] = $bearerToken;
    }

    private function request(string $method, string $url): string|array
    {
        $response = $this->budgetInsightClient->request($method, $url, $this->options);

        return $response->getContent(false);
    }
}