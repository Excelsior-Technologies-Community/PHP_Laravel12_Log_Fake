# PHP_Laravel12_Log_Fake 

## Project Description

PHP_Laravel12_Log_Fake is a simple Laravel 12 package-based project that demonstrates how to create a custom fake logging system for testing purposes.

In many Laravel applications, logs are written to files. However, during automated testing it is useful to capture logs in memory instead of writing them to disk. This project implements a fake logger that follows the PSR-3 Logger Interface and stores log records internally.

Developers can then verify whether specific log messages were triggered during tests. This helps ensure that application events such as user login, errors, or warnings are correctly logged.

The project also demonstrates how to build and register a custom Laravel package inside a project, including service providers, autoloading, and local package development using Composer.


## Features

- Custom Fake Logger implementation
- Captures log messages in memory instead of files
- Supports all standard logging levels
- Provides a method to assert whether a log message was recorded
- Demonstrates how to create a local Laravel package
- Uses Service Providers for package registration
- Integrates with Laravel's logging system using the Log facade
- Includes automated testing using PHPUnit
- Shows how to replace the default logger during tests


## Technologies Used

- PHP 8+
- Laravel 12 Framework
- Composer (Dependency Management)
- PSR-3 Logger Interface
- PHPUnit (Testing Framework)
- Laravel Service Providers
- Laravel Facades
- Local Package Development Structure


## How It Works

1. A custom package is created inside the `packages` directory.
2. The package contains a fake logger class that implements the PSR-3 LoggerInterface.
3. Instead of writing logs to files, the fake logger stores them in memory.
4. During testing, Laravel's default logger is replaced with the fake logger.
5. Tests can then verify whether specific log messages were triggered.



---



## Installation Steps


---


## STEP 1: Create Laravel 12 Project

### Open terminal / CMD and run:

```
composer create-project laravel/laravel PHP_Laravel12_Log_Fake "12.*"

```

### Go inside project:

```
cd PHP_Laravel12_Log_Fake

```

#### Explanation:

This command installs a fresh Laravel 12 application and creates the project folder.

The cd command moves into the newly created project directory so you can start working on it.




## STEP 2: Database Setup (Optional)

### Update database details:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel12_Log_Fake
DB_USERNAME=root
DB_PASSWORD=

```

### Create database in MySQL / phpMyAdmin:

```
Database name: laravel12_Log_Fake

```

### Then Run:

```
php artisan migrate

```


#### Explanation:

This step connects Laravel with your MySQL database and runs default migrations to create basic tables such as users, password_reset_tokens, and sessions.




## STEP 3: CREATE PACKAGE STRUCTURE

### In your project root, make packages directory:

```
mkdir -p packages/Timacdonald/LogFake/src

```

### Your directory should look like:

```
PHP_Laravel12_Log_Fake/
  packages/
    Timacdonald/
      LogFake/
        src/

```

#### Explanation:

This step creates a custom package folder inside the Laravel project.

The src directory will contain the main package code such as the fake logger and service provider.





## STEP 4: SETUP composer.json for the package

### Create file:

```
packages/Timacdonald/LogFake/composer.json

```

### Paste:

```
{
    "name": "timacdonald/log-fake",
    "description": "Fake logger for Laravel testing",
    "type": "library",
    "license": "MIT",
    "autoload": {
        "psr-4": {
            "Timacdonald\\LogFake\\": "src/"
        }
    },
    "extra": {
        "laravel": {
            "providers": [
                "Timacdonald\\LogFake\\LogFakeServiceProvider"
            ]
        }
    }
}

```

#### Explanation:

This file defines the package configuration for Composer.

It registers the namespace, dependencies, and automatically loads the service provider in Laravel.






## STEP 5: REGISTER Package in root composer.json

### Open root composer.json and edit:

```
"repositories": [
    {
        "type": "path",
        "url": "packages/Timacdonald/LogFake"
    }
]


```

### Then run:

```
composer require timacdonald/log-fake:@dev

```

#### Explanation:

This step tells Composer to load the package from the local packages directory instead of Packagist.

The composer require command installs the package inside the Laravel project.





## STEP 6: CREATE SERVICE PROVIDER

### Create:

```
packages/Timacdonald/LogFake/src/LogFakeServiceProvider.php

```

### Paste:

```
<?php

namespace Timacdonald\LogFake;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class LogFakeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('log.fake', function ($app) {
            return new LogFake();
        });
    }

    public function boot()
    {
        // Bind fake into logger
        Log::extend('fake', function () {
            return new LogFake;
        });
    }
}

```

#### Explanation:

The Service Provider registers the fake logger inside Laravel's service container.

It allows the application to use the custom fake logger instead of the default Laravel logger during testing.






## STEP 7: CREATE THE FAKE LOGGER

### Create:

```
packages/Timacdonald/LogFake/src/LogFake.php

```

### Paste:

```
<?php

namespace Timacdonald\LogFake;

use Psr\Log\LoggerInterface;
use Stringable;

class LogFake implements LoggerInterface
{
    protected array $records = [];

    public function emergency(Stringable|string $message, array $context = []): void
    {
        $this->write('emergency', $message, $context);
    }

    public function alert(Stringable|string $message, array $context = []): void
    {
        $this->write('alert', $message, $context);
    }

    public function critical(Stringable|string $message, array $context = []): void
    {
        $this->write('critical', $message, $context);
    }

    public function error(Stringable|string $message, array $context = []): void
    {
        $this->write('error', $message, $context);
    }

    public function warning(Stringable|string $message, array $context = []): void
    {
        $this->write('warning', $message, $context);
    }

    public function notice(Stringable|string $message, array $context = []): void
    {
        $this->write('notice', $message, $context);
    }

    public function info(Stringable|string $message, array $context = []): void
    {
        $this->write('info', $message, $context);
    }

    public function debug(Stringable|string $message, array $context = []): void
    {
        $this->write('debug', $message, $context);
    }

    public function log($level, Stringable|string $message, array $context = []): void
    {
        $this->write($level, $message, $context);
    }

    protected function write($level, $message, array $context = []): void
    {
        $this->records[] = [
            'level' => $level,
            'message' => (string) $message,
            'context' => $context
        ];
    }

    public function records(): array
    {
        return $this->records;
    }

    public function assertLogged($level, $message = null): bool
    {
        foreach ($this->records as $record) {
            if (
                $record['level'] === $level &&
                ($message === null || str_contains($record['message'], $message))
            ) {
                return true;
            }
        }

        throw new \Exception("Log [$level] containing [$message] not found.");
    }
}

```

#### Explanation:

This class implements the PSR-3 LoggerInterface and captures log messages in memory instead of writing them to files.

It also provides the assertLogged() method to verify that specific log messages were recorded during tests.





## STEP 8: CREATE TEST TO USE LOG FAKE

### Install PHPUnit (already in Laravel):

```
composer require --dev phpunit/phpunit

```

### Create test:

```
tests/Feature/LogFakeTest.php

```

### Paste:

```
<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Log;

class LogFakeTest extends TestCase
{
    public function testFakeLog()
    {
        Log::swap(app('log.fake'));

        Log::info('User logged in');

        $fake = app('log.fake');

        $this->assertTrue(
            $fake->assertLogged('info', 'User logged in')
        );
    }
}

```

#### Explanation:

This test replaces Laravel's normal logger with the fake logger and writes a log message.

Then it checks if the fake logger correctly stored the log using the assertLogged() method.





## STEP 9: RUN TEST

### Run:

```
php artisan test

```

### You should see:

```

   PASS  Tests\Unit\ExampleTest
  ✓ that true is true

   PASS  Tests\Feature\ExampleTest
  ✓ the application returns a successful response                                                                0.22s

   PASS  Tests\Feature\LogFakeTest
  ✓ fake log                                                                                                     0.02s

  Tests:    3 passed (3 assertions)
  Duration: 0.42s

```

#### Explanation:

This command runs all Laravel tests using PHPUnit.

If the fake logger works correctly, the test will pass and confirm that the logging system was successfully mocked.


## Expected Output:


<img width="1452" height="362" alt="Screenshot 2026-03-12 110409" src="https://github.com/user-attachments/assets/49b4470e-29aa-4cc2-82eb-0cd5dc3640aa" />



---

# Project Folder Structure:

```
PHP_Laravel12_Log_Fake
│
├── app
│
├── bootstrap
│
├── config
│
├── database
│
├── packages
│   └── Timacdonald
│       └── LogFake
│           ├── composer.json
│           ├── README.md
│           └── src
│               ├── LogFake.php
│               └── LogFakeServiceProvider.php
│
├── routes
│   └── web.php
│
├── storage
│
├── tests
│   ├── Unit
│   │   └── ExampleTest.php
│   │
│   └── Feature
│       ├── ExampleTest.php
│       └── LogFakeTest.php
│
├── vendor
│
├── .env
├── .env.example
├── .gitignore
├── artisan
├── composer.json
├── package.json
├── phpunit.xml
└── README.md

```
