<?php

define('REPOS_JSON_FILE_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'repos.json');

$repos = json_decode(file_get_contents(REPOS_JSON_FILE_PATH), true);
$modules = [];

foreach ($repos as $repo) {
    $matches = [];
    preg_match('/git@github.com:(.+)\/(.+)\.git/', $repo['github'], $matches);
    if (!empty($matches[1]) && !empty($matches[2])) {
        $username = $matches[1];
        $repoName = $matches[2];
        $manifestContent = file_get_contents("http://raw.githubusercontent.com/{$username}/{$repoName}/master/manifest.json");
        if ($manifestContent !== false) {
            $manifest = json_decode($manifestContent, true);
            if ($manifest) {
                $modules[] = array_merge([
                    'repo' => $repo,
                ], [
                    'manifest' => $manifest,
                ]);
            }
        }
    }
}

echo json_encode($modules, JSON_PRETTY_PRINT);
