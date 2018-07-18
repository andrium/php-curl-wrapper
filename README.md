# The simplest wrapper for CURL on PHP

__CURL Easy example:__

```php
try {
    $easy = new CURL\Easy([
        CURLOPT_URL => 'http://api.ipify.org/?format=text',
        CURLOPT_PROTOCOLS => CURLPROTO_HTTP|CURLPROTO_HTTPS,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_AUTOREFERER => true,
        CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTP|CURLPROTO_HTTPS,
        CURLOPT_MAXREDIRS => 1,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30
    ]);

    $content = $easy->execute();

    echo $content . PHP_EOL;
} catch (CURL\Easy\Error $e) {
    echo $e . PHP_EOL;
}
```

__CURL Multi example:__

```php
try {
    $collection = new CURL\Multi();

    $template = new CURL\Easy([
        CURLOPT_PROTOCOLS => CURLPROTO_HTTP|CURLPROTO_HTTPS,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_AUTOREFERER => true,
        CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTP|CURLPROTO_HTTPS,
        CURLOPT_MAXREDIRS => 1,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30
    ]);

    $collection[] = (clone $template)
        ->setOption(CURLOPT_URL, 'http://api.ipify.org/?format=text');

    $collection->execute();

    foreach ($collection as $name => $request) {
        $error = $request->getError();
        if ($error === null) {
            $content = $request->getContent();

            echo "[{$name}] => DONE: {$content}" . PHP_EOL;
        } else {
            echo "[{$name}] => FAIL: {$error}" . PHP_EOL;
        }
    }
} catch (CURL\Multi\Error $e) {
    echo $e . PHP_EOL;
}
```
