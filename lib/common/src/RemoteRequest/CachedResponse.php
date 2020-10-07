<?php

/**
 * Class CachedResponse.
 *
 * @package AmpProject\AmpWP
 */

namespace AmpProject\RemoteRequest;

use AmpProject\Response;
use DateTimeImmutable;
use DateTimeInterface;

/**
 * Serializable object that represents a cached response together with its expiry time.
 *
 * @package AmpProject\AmpWP
 * @since 2.0
 * @internal
 */
final class CachedResponse implements Response
{

    /**
     * Response that is being cached.
     *
     * @var RemoteGetRequestResponse
     */
    private $response;

    /**
     * Expiry time of the cached value.
     *
     * @var DateTimeInterface
     */
    private $expiry;

    /**
     * Instantiate a CachedResponse object.
     *
     * @param Response          $response Response object to cache.
     * @param DateTimeInterface $expiry   Expiry of the cached value.
     */
    public function __construct(Response $response, DateTimeInterface $expiry)
    {
        $this->response = $response;
        $this->expiry   = $expiry;
    }

    /**
     * Instantiate a CachedResponse object for a fresh Response instance.
     *
     * @param string            $body       Cached body.
     * @param string[][]        $headers    Associative array of cached headers.
     * @param int               $statusCode Cached status code.
     * @param DateTimeInterface $expiry     Expiry of the cached value.
     *
     * @return self
     */
    public static function withNewResponse($body, $headers, $statusCode, DateTimeInterface $expiry)
    {
        return new self(
            new RemoteGetRequestResponse(
                (string) $body,
                (array) $headers,
                (int) $statusCode
            ),
            $expiry
        );
    }

    /**
     * Get the body of the response.
     *
     * @return string Body of the response.
     */
    public function getBody()
    {
        return $this->response->getBody();
    }

    /**
     * Retrieves all message header values.
     *
     * The keys represent the header name as it will be sent over the wire, and each value is an array of strings
     * associated with the header.
     *
     *     // Represent the headers as a string
     *     foreach ($message->getHeaders() as $name => $values) {
     *         echo $name . ': ' . implode(', ', $values);
     *     }
     *
     *     // Emit headers iteratively:
     *     foreach ($message->getHeaders() as $name => $values) {
     *         foreach ($values as $value) {
     *             header(sprintf('%s: %s', $name, $value), false);
     *         }
     *     }
     *
     * While header names are not case-sensitive, getHeaders() will preserve the exact case in which headers were
     * originally specified.
     *
     * @return string[][] Returns an associative array of the message's headers. Each key MUST be a header name, and
     *                    each value MUST be an array of strings for that header.
     */
    public function getHeaders()
    {
        return $this->response->getHeaders();
    }

    /**
     * Checks if a header exists by the given case-insensitive name.
     *
     * @param string $name Case-insensitive header field name.
     * @return bool Returns true if any header names match the given header name using a case-insensitive string
     *                     comparison. Returns false if no matching header name is found in the message.
     */
    public function hasHeader($name)
    {
        return $this->response->hasHeader($name);
    }

    /**
     * Retrieves a message header value by the given case-insensitive name.
     *
     * This method returns an array of all the header values of the given case-insensitive header name.
     *
     * If the header does not appear in the message, this method MUST return an empty array.
     *
     * @param string $name Case-insensitive header field name.
     * @return string[] An array of string values as provided for the given header. If the header does not appear in
     *                     the message, this method MUST return an empty array.
     */
    public function getHeader($name)
    {
        return $this->response->getHeader($name);
    }

    /**
     * Retrieves a comma-separated string of the values for a single header.
     *
     * This method returns all of the header values of the given case-insensitive header name as a string concatenated
     * together using a comma.
     *
     * NOTE: Not all header values may be appropriately represented using comma concatenation. For such headers, use
     * getHeader() instead and supply your own delimiter when concatenating.
     *
     * If the header does not appear in the message, this method MUST return an empty string.
     *
     * @param string $name Case-insensitive header field name.
     * @return string A string of values as provided for the given header concatenated together using a comma. If the
     *                header does not appear in the message, this method MUST return an empty string.
     */
    public function getHeaderLine($name)
    {
        return $this->response->getHeaderLine($name);
    }

    /**
     * Gets the response status code.
     *
     * The status code is a 3-digit integer result code of the server's attempt to understand and satisfy the request.
     *
     * @return int Status code.
     */
    public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }

    /**
     * Determine the validity of the cached response.
     *
     * @return bool Whether the cached response is valid.
     */
    public function isValid()
    {
        // Values are already typed, so we just control the status code for validity.
        return $this->response->getStatusCode() > 100
            && $this->response->getStatusCode() <= 599;
    }

    /**
     * Get the expiry of the cached value.
     *
     * @return DateTimeInterface Expiry of the cached value.
     */
    public function getExpiry()
    {
        return $this->expiry;
    }

    /**
     * Check whether the cached value is expired.
     *
     * @return bool Whether the cached value is expired.
     */
    public function isExpired()
    {
        return new DateTimeImmutable('now') > $this->expiry;
    }
}
