<?php

namespace LonghornOpen\LaravelCelticLTI\Adaptors;

use ceLTIc\LTI\Http\ClientInterface;
use ceLTIc\LTI\Http\HttpMessage;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

class LonghornLaravelHttpClient implements ClientInterface
{

    /**
     * @inheritDoc
     */
    public function send(HttpMessage $message): bool
    {
        $message->ok = false;
        $message->status = 0;
        $message->error = '';
        $message->response = null;
        $message->responseHeaders = [];

        $headers = [];
        foreach ($message->requestHeaders as $header) {
            if (!str_contains($header, ':')) {
                continue;
            }
            [$name, $value] = explode(':', $header, 2);
            $headers[strtolower(trim($name))][] = trim($value);
        }

        // HttpMessage has already encoded the body; do not JSON-encode it again.
        $options = [];
        if ($message->getMethod() !== 'GET' && $message->request !== null) {
            $options['body'] = $message->request;
            $headers['content-type'] ??= ['application/x-www-form-urlencoded'];
        }

        $request = Http::connectTimeout(30)
            ->timeout(30)
            ->withoutRedirecting()
            ->withHeaders($headers)
            ->beforeSending(function (Request $request) use ($message) {
                $psrRequest = $request->toPsrRequest();
                $message->requestHeaders = array_merge([
                    $psrRequest->getMethod() . ' ' . $psrRequest->getRequestTarget()
                        . ' HTTP/' . $psrRequest->getProtocolVersion(),
                ], $this->headerLines($psrRequest->getHeaders()));
            });

        try {
            $response = $request->send($message->getMethod(), $message->getUrl(), $options);
        } catch (ConnectionException $exception) {
            $message->error = $exception->getMessage();
            return false;
        }

        $psrResponse = $response->toPsrResponse();
        $message->responseHeaders = array_merge([
            'HTTP/' . $psrResponse->getProtocolVersion() . ' ' . $response->status()
                . ' ' . $response->reason(),
        ], $this->headerLines($response->headers()));
        $message->response = $response->body();
        $message->status = $response->status();
        $message->ok = $message->status >= 100 && $message->status < 400;

        return $message->ok;
    }

    /**
     * Convert HTTP header maps to the individual lines expected by ceLTIc.
     */
    private function headerLines(array $headers): array
    {
        $lines = [];
        foreach ($headers as $name => $values) {
            foreach ($values as $value) {
                $lines[] = $name . ': ' . $value;
            }
        }

        return $lines;
    }
}
