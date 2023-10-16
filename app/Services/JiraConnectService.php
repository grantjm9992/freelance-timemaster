<?php

namespace App\Services;

use Unirest\Request;
use Unirest\Response;

class JiraConnectService
{
    public function __construct(

    ) {
    }

    public function __invoke(string $dataType): array
    {
        Request::auth(env('JIRA_EMAIL_ADDRESS'), env('JIRA_API_TOKEN'));

        $response = $dataType === 'issue' ? $this->getIssues() : $this->getProjects();

        return json_decode($response->raw_body, true);
    }

    public function getIssues(): Response
    {
        $headers = array(
            'Accept' => 'application/json'
        );

        $query = array(
            'jql' => '',
            'maxResults' => 100,
            'fields' => 'summary',
        );

        return Request::get(
            env('JIRA_API_URL').'/rest/api/3/search',
            $headers,
            $query
        );
    }

    private function getProjects(): Response
    {
        $headers = array(
            'Accept' => 'application/json'
        );

        return Request::get(
            env('JIRA_API_URL').'/rest/api/3/project/search',
            $headers
        );
    }
}
