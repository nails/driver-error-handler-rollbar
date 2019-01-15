# Rollbar Error Hanlder

![license](https://img.shields.io/badge/license-MIT-green.svg)
[![CircleCI branch](https://img.shields.io/circleci/project/github/nails/driver-error-handler-rollbar.svg)](https://circleci.com/gh/nails/driver-error-handler-rollbar)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/nails/driver-error-handler-rollbar/badges/quality-score.png)](https://scrutinizer-ci.com/g/nails/driver-error-handler-rollbar)
[![Join the chat on Slack!](https://now-examples-slackin-rayibnpwqe.now.sh/badge.svg)](https://nails-app.slack.com/shared_invite/MTg1NDcyNjI0ODcxLTE0OTUwMzA1NTYtYTZhZjc5YjExMQ)

This driver provides support for reporting errors to rollbar.com


## Installing

    composer require nails/driver-error-handler-rollbar


## Configure

The following constants must be defined:

| Constant                    | Description                             |
|-----------------------------|-----------------------------------------|
| DEPLOY_ROLLBAR_ACCESS_TOKEN | The server access token for the project |
