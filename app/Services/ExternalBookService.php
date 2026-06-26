<?php

namespace App\Services;

use App\Helpers\ApiRequest;
use App\Models\Book;
use App\Repositories\BookRepository;

class ExternalBookService
{
    private BookRepository $bookRepository;

    private string $googleBooksUrl;
    private string $externalApiUrl;

    public function __construct()
    {
        $this->bookRepository = new BookRepository();

        $this->googleBooksUrl = $_ENV['GOOGLE_BOOKS_API_URL'] ?? 'https://www.googleapis.com/books/v1/volumes';
        $this->externalApiUrl = $_ENV['EXTERNAL_BOOK_API_URL'] ?? 'https://www.mann-ivanov-ferber.ru/book/search.ajax';
    }

    public function searchBooks(string $query): array
    {
        if (empty($query)) {
            return ['error' => 'Search query is required'];
        }

        error_log('Search query: ' . $query);

        $googleBooks = $this->searchGoogleBooks($query);
        error_log('Google Books results: ' . count($googleBooks));

        $externalBooks = $this->searchExternalApi($query);
        error_log('External API results: ' . count($externalBooks));

        $results = array_merge($googleBooks, $externalBooks);

        if (empty($results)) {
            return ['error' => 'No books found'];
        }

        return ['books' => $results];
    }

    private function searchGoogleBooks(string $query): array
    {
        $url = $this->googleBooksUrl . '?q=' . urlencode($query) . '&maxResults=10';
        $data = ApiRequest::get($url);

        if (!$data || !isset($data['items'])) {
            return [];
        }

        $books = [];
        foreach ($data['items'] as $item) {
            $volumeInfo = $item['volumeInfo'] ?? [];
            $books[] = [
                'id' => $item['id'] ?? null,
                'title' => $volumeInfo['title'] ?? 'Unknown title',
                'description' => $volumeInfo['description'] ?? 'No description',
                'url' => $volumeInfo['infoLink'] ?? null,
                'source' => 'Google Books',
            ];
        }

        return $books;
    }

    private function searchExternalApi(string $query): array
    {
        $url = $this->externalApiUrl . '?q=' . urlencode($query);
        $data = ApiRequest::get($url);

        if (empty($data)) {
            return [];
        }

        $books = [];

        foreach ($data as $section) {
            if (!isset($section['sectionType']) || $section['sectionType'] !== 'books') {
                continue;
            }

            if (!isset($section['items']) || !is_array($section['items'])) {
                continue;
            }

            foreach ($section['items'] as $item) {
                $books[] = [
                    'id' => $item['id'] ?? null,
                    'title' => $item['title'] ?? 'Unknown title',
                    'description' => $item['authors'] ?? 'No authors listed',
                    'url' => $item['url'] ?? null,
                    'source' => 'Mann-Ivanov-Ferber',
                ];
            }
        }

        return $books;
    }

    public function saveBook(int $userId, string $externalId, array $searchResults): array
    {
        $foundBook = null;
        foreach ($searchResults as $book) {
            if ($book['id'] == $externalId) {
                $foundBook = $book;
                break;
            }
        }

        if (!$foundBook) {
            return ['error' => 'Book not found'];
        }

        $content = $foundBook['description'] . "\n\nURL: " . ($foundBook['url'] ?? 'No URL');

        $book = new Book(
            $userId,
            $foundBook['title'],
            $content
        );

        $saved = $this->bookRepository->save($book);

        if (!$saved) {
            return ['error' => 'Failed to save book'];
        }

        return [
            'message' => 'Book saved successfully',
            'book' => [
                'id' => $book->getId(),
                'title' => $book->getTitle(),
            ],
        ];
    }
}
