![php](https://img.shields.io/badge/php-%3E%3D%208.1-8892BF.svg)

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
cd php-dependency-scanner/.git/hooks && ln -s ../../git-hooks/* . && cd ../..
composer install
```