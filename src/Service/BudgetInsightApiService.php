<?php

namespace App\Service;

use App\Entity\AccountType;
use App\Entity\Connector;
use App\Exception\SynchronizationException;
use App\Model\BankAccount;
use App\Model\Connection;
use App\Model\Investment;
use App\Model\PermanentUserAccessToken;
use App\Model\TemporaryCode;
use App\Model\Transaction;
use App\Model\UserAccessToken;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BudgetInsightApiService
{
    const USER_ACCESS_TOKEN_ENDPOINT = '/auth/init';
    const TEMPORARY_CODE_ENDPOINT = '/auth/token/code';
    const PERMANENT_USER_ACCESS_TOKEN_ENDPOINT = '/auth/token/access';

    const LIST_BANK_ACCOUNTS_ENDPOINT = '/users/me/accounts';
    const LIST_CONNECTIONS_ENDPOINT = '/users/me/connections';
    const LIST_INVESTMENTS_ENDPOINT = '/users/me/investments';
    const LIST_TRANSACTIONS_ENDPOINT = 'users/me/accounts/{id}/transactions';
    const SYNC_CONNECTION_ENDPOINT = '/users/me/connections/{id}';

    const LIST_CONNECTORS_ENDPOINT = '/connectors';
    const LIST_ACCOUNT_TYPES_ENDPOINT = '/account_types';

    private array $options = [];

    public function __construct(
        private HttpClientInterface   $budgetInsightClient,
        private SerializerInterface   $serializer,
        private Security              $security,
        private ParameterBagInterface $parameters
    )
    {
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

    public function getBankAccount(int $id): BankAccount
    {
        $this->useBearerToken();

        $baseUrl = $this->parameters->get('base_url');

        $data = $this->request(Request::METHOD_GET, "$baseUrl/users/me/accounts/$id");

        return $this->serializer->deserialize($data, BankAccount::class, 'json');
    }

    /**
     * @return BankAccount[]
     */
    public function listBankAccounts(?string $type = null): array
    {
        $response = [];

        $this->useBearerToken();

        $data = json_decode($this->request('GET', self::LIST_BANK_ACCOUNTS_ENDPOINT), true);

        /** @var BankAccount[] $accounts */
        $accounts = $this->serializer->deserialize(json_encode($data['accounts']), 'App\Model\BankAccount[]', 'json');

        if (!$type) {
            return $accounts;
        }

        foreach ($accounts as $account) {
            if ($account->type === $type) {
                $response[] = $account;
            }
        }

        return $response;
    }

    /**
     * @return Transaction[]
     */
    public function listTransactions(int $accountId, int $offset = 0, int $limit = 10, ?\DateTime $minDate = null, ?\DateTime $maxDate = null): array
    {
        $this->useBearerToken();

        $url = "users/me/accounts/$accountId/transactions";

        $query = [
            'offset' => $offset,
            'limit' => $limit
        ];

        if ($minDate) {
            $query['min_date'] = $minDate->format('Y-m-d');
        }

        if ($minDate) {
            $query['max_date'] = $maxDate->format('Y-m-d');
        }

        $this->options['query'] = $query;

        $data = json_decode($this->request('GET', $url), true);

        return $this->serializer->deserialize(json_encode($data['transactions']), 'App\Model\Transaction[]', 'json');
    }

    /**
     * @return Connection[]
     */
    public function listConnections(): array
    {
        $this->useBearerToken();

        $data = json_decode($this->request('GET', self::LIST_CONNECTIONS_ENDPOINT), true);

        return $this->serializer->deserialize(json_encode($data['connections']), 'App\Model\Connection[]', 'json');
    }

    /**
     * @return Connector[]
     */
    public function listConnectors(): array
    {
        $data = json_decode($this->request('GET', self::LIST_CONNECTORS_ENDPOINT), true);

        return $this->serializer->deserialize(json_encode($data['connectors']), 'App\Entity\Connector[]', 'json');
    }

    /**
     * @return Investment[]
     */
    public function listInvestments(): array
    {
        $this->useBearerToken();

        $data = json_decode($this->request('GET', self::LIST_INVESTMENTS_ENDPOINT), true);

        return $this->serializer->deserialize(json_encode($data['investments']), 'App\Model\Investment[]', 'json');

    }

    public function listInvestmentsByAccount(int $id): array
    {
        $this->useBearerToken();

        $baseUrl = $this->parameters->get('base_url');

        $data = json_decode($this->request('GET', "$baseUrl/users/me/accounts/$id/investments"), true);

        return $this->serializer->deserialize(json_encode($data['investments']), 'App\Model\Investment[]', 'json');
    }

    /**
     * @return AccountType[]
     */
    public function listAccountTypes(): array
    {
        $data = json_decode($this->request('GET', self::LIST_ACCOUNT_TYPES_ENDPOINT), true);

        return $this->serializer->deserialize(json_encode($data['accounttypes']), 'App\Entity\AccountType[]', 'json');
    }

    public function manageConnections(string $token): string
    {
        $baseUrl = $this->parameters->get('base_url');

        $data = [
            'client_id' => $this->parameters->get('client_id'),
            'code' => $this->generateTemporaryCode($token)->code,
            'redirect_uri' => 'https://127.0.0.1:8000/api/users/me/webview'
        ];

        $query = http_build_query($data);

        return "$baseUrl/auth/webview/fr/manage?$query";
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

        return $this->serializer->deserialize($data, Connection::class, 'json');
    }

    private function useBearerToken(): void
    {
        $bearerToken = $this->security->getUser()->getBearerToken();

        $this->options['auth_bearer'] = $bearerToken;
    }

    /**
     * @throws SynchronizationException
     */
    private function request(string $method, string $url): string|array
    {
        $response = $this->budgetInsightClient->request($method, $url, $this->options);

        try {
            return $response->getContent();
        } catch (\Exception $e) {
            return $this->handleRequestException($e->getCode());
        }
    }

    /**
     * @throws SynchronizationException
     */
    private function handleRequestException(int $code): array
    {
        $data = [];

        switch ($code) {
            case Response::HTTP_CONFLICT:
                throw new SynchronizationException("Can't force synchronization of connection");
            default:
                break;
        }

        return $data;
    }
}