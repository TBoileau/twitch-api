# SDK for Twitch API

## Installation

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require TBoileau/twitch-api
```

### Step 2: Install the Twitch Cli

Open a command console, enter your project directory and execute the
following command to download the Twitch Cli :

```console
$ php bin/console twitch:install -v 1.1.21 -d Linux_x86_64
```

Options :
* **--version (or -v)** : Pick one of the available versions (default: 1.1.12), you can check all of the available versions [here](https://github.com/twitchdev/twitch-cli/releases).
* **--distribution (or -d)** : Choose the right distribution for your system (Linux_x86_64, Linux_arm64, Darwin_x86_64, Darwin_arm64, Windows_x86_64, Windows_i386). 

## Usage

### Configure the Twitch API environment variables

```dotenv
TWITCH_MOCK_SERVER_PORT=8080 # you can leave it empty if you don't want to use the mock server
TWITCH_API_HOST=https://api.twitch.tv # or http://localhost:8080 if you want to use the mock server
TWITCH_API_BASE_URI=/helix # or /mock if you want to use the mock server
TWITCH_API_CLIENT_ID=your_client_id # you can leave it empty if you use the mock server
TWITCH_API_CLIENT_SECRET=your_client_secret # you can leave it empty if you use the mock server
```

### Start the Twitch mock server (optional)

Open a command console, enter your project directory and execute the following command to start the Twitch mock server :

```console
$ php bin/console twitch:serve
```

### Send a request

```php
<?php

use TBoileau\TwitchApi\Api\TwitchApiFactory;

$twitchApi = TwitchApiFactory::create($accessToken, $_ENV['TWITCH_API_CLIENT_ID']);

$leaderboard = $twitchApi->Bits->getLeaderboard();
```
