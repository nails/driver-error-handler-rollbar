# Rollbar Error Hanlder

![license](https://img.shields.io/badge/license-MIT-green.svg)
[![CircleCI branch](https://img.shields.io/circleci/project/github/nails/driver-error-handler-rollbar.svg)](https://circleci.com/gh/nails/driver-error-handler-rollbar)

This driver provides support for reporting errors to rollbar.com


## Installing

    composer require nails/driver-error-handler-rollbar


## Configure

The following constants must be defined:

| Constant                    | Description                             |
|-----------------------------|-----------------------------------------|
| DEPLOY_ROLLBAR_ACCESS_TOKEN | The server access token for the project |
