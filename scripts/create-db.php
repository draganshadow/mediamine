<?php
echo exec(__DIR__ . '/../vendor/bin/doctrine-module orm:schema-tool:drop --force') . PHP_EOL;
echo exec(__DIR__ . '/../vendor/bin/doctrine-module orm:schema-tool:create') . PHP_EOL;
echo exec('rm -rf ../public/db/*') . PHP_EOL;
echo exec('java -jar schemaSpy.jar -t pgsql -host localhost:5432 -u postgres -p postgres -o ../public/db -dp postgresql-9.1-903.jdbc3.jar -db mediamine -s public') . PHP_EOL;