<?php

namespace App\Model\PowensApi;

enum ConnectionState {
    case SCARequired;
    case webauthRequired;
    case additionalInformationNeeded;
    case decoupled;
    case validating;
    case actionNeeded;
    case passwordExpired;
    case wrongpass;
    case rateLimiting;
    case websiteUnavailable;
    case bug;
    case notSupported;
}