<?php

// Replace these values with your own API key, API secret key, and Bearer token
$api_key = 'hqkNlE24A5BiKlyLxqDvBasAk';
$api_secret_key = '1TYvepd0sfGoSlGnW6BABCggeoCTV8oJ4ib2NoPvCpOdKnYOVK';
$bearer_token = 'AAAAAAAAAAAAAAAAAAAAAKIRkwEAAAAAeVhsMtlHxrov4PRP%2BFfKEofomyk%3DEi95GrqqmrkRqPzFvhn0PbzQW6CiEWx3LlHGzBDpNjfucjQ2jz';

// Replace this value with the user ID of the user whose Tweet timeline you want to retrieve
$user_id = '2819050825';

// Use the curl function to make a GET request to the user Tweet timeline endpoint
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.twitter.com/2/users/$user_id/tweets");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  "Authorization: Bearer $bearer_token",
  "x-api-key: $api_key",
  "x-api-secret-key: $api_secret_key"
));

$response = curl_exec($ch);
curl_close($ch);

// Parse the JSON response
$tweets = json_decode($response, true);

// Output the tweets as a JavaScript array
echo '<script>';
echo 'var tweets = ' . json_encode($tweets) . ';';
echo '</script>';

?>