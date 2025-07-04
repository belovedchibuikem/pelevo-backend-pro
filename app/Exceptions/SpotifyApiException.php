<?php
// app/Exceptions/SpotifyApiException.php
namespace App\Exceptions;

use Exception;

class SpotifyApiException extends Exception
{
    protected $spotifyError;

    public function __construct(string $message, int $code = 0, array $spotifyError = [], Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->spotifyError = $spotifyError;
    }

    public function getSpotifyError(): array
    {
        return $this->spotifyError;
    }

    public function render()
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
            'spotify_error' => $this->spotifyError
        ], $this->getCode() ?: 500);
    }
}
