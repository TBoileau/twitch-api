# CHANGELOG
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## [1.0.4] 2023-11-23
### Updated
* Make the method call to set HttpClient to AbstractOperations lazy

## [1.0.3] 2023-11-22
### Updated
* Throw TwitchUnauthorizedException only if response status code is equal to 401

## [1.0.2] 2023-11-22
### Add
* Allow to throw http exceptions
* Try catch and throw TwitchUnauthorizedException

## [1.0.1] 2023-11-22
### Updated
* Add iterator_to_array
* Add ghost properties
* Add public to properties
* Replace timestamp by datetime iso

## [1.0.0-alpha] 2023-11-22
### Added
* Download Twitch Cli
* Start the Twitch Mock Server
* Endpoint : Get Bits Leaderboard
* Endpoint : Get Channel Followers
* Endpoint : Get Broadcaster Subscriptions
