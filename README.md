![php](https://img.shields.io/badge/php-%3E%3D%208.1-8892BF.svg)
[![codecov](https://codecov.io/gh/locr-company/php-dependency-scanner/branch/main/graph/badge.svg?token=FsUYYO0nve)](https://codecov.io/gh/locr-company/php-dependency-scanner)
![github_workflow_status](https://img.shields.io/github/actions/workflow/status/locr-company/php-dependency-scanner/php-8.1.yml)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=locr-company_php-dependency-scanner&metric=alert_status)](https://sonarcloud.io/summary/new_code?id=locr-company_php-dependency-scanner)
[![github_tag](https://img.shields.io/github/v/tag/locr-company/php-dependency-scanner)](https://github.com/locr-company/php-dependency-scanner/tags)
[![packagist](https://img.shields.io/packagist/v/locr-company/dependency-scanner)](https://packagist.org/packages/locr-company/dependency-scanner)

# 1. Installation

```bash
composer require locr-company/dependency-scanner
```

# 2. How to use

```php
<?php

use Locr\Lib\Dependencies;

$deps = Dependencies::getDependencies(path: '/path/to/project');
```

# 3. Development

Clone the repository

```bash
git clone git@github.com:locr-company/php-dependency-scanner.git
cd php-dependency-scanner && composer install
```