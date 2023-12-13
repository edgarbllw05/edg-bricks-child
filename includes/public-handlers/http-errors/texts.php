<?php


$server_error = 'The server encountered an internal error or misconfiguration and was unable to complete your request.';

return [
	400 => [
		'title' => 'Bad Request',
		'message' => 'Your browser sent a request that this server could not understand.',
	],
	401 => [
		'title' => 'Unauthorized',
		'message' => 'This server could not verify that you are authorized to access the document requested. Either you supplied the wrong credentials (e. g., bad password), or your browser doesn\'t understand how to supply the credentials required.',
	],
	402 => [
		'title' => 'Payment Required',
		'message' => $server_error,
	],
	403 => [
		'title' => 'Forbidden',
		'message' => 'You don\'t have permission to access this resource.',
	],
	404 => [
		'title' => 'Not Found',
		'message' => 'The requested URL was not found on this server.',
	],
	405 => [
		'title' => 'Method Not Allowed',
		'message' => 'The requested method is not allowed for this URL.',
	],
	406 => [
		'title' => 'Not Acceptable',
		'message' => 'An appropriate representation of the requested resource could not be found on this server.',
	],
	408 => [
		'title' => 'Request Timeout',
		'message' => 'Server timeout waiting for the HTTP request from the client.',
	],
	409 => [
		'title' => 'Conflict',
		'message' => $server_error,
	],
	410 => [
		'title' => 'Gone',
		'message' => 'The requested resource is no longer available on this server and there is no forwarding address. Please remove all references to this resource.',
	],
	411 => [
		'title' => 'Length Required',
		'message' => 'A request of the requested method requires a valid content-length.',
	],
	412 => [
		'title' => 'Precondition Failed',
		'message' => 'The precondition on the request for this URL evaluated to false.',
	],
	413 => [
		'title' => 'Request Entity Too Large',
		'message' => 'The requested resource does not allow request data with this number of requests, or the amount of data provided in the request exceeds the capacity limit.',
	],
	414 => [
		'title' => 'Request URL Too Long',
		'message' => 'The requested URL\'s length exceeds the capacity limit for this server.',
	],
	415 => [
		'title' => 'Unsupported Media Type',
		'message' => 'The supplied request data is not in a format acceptable for processing by this resource.',
	],
	416 => [
		'title' => 'Requested Range Not Satisfiable',
		'message' => 'None of the range-specifier values in the Range request header field overlap the current extent of the selected resource.',
	],
	417 => [
		'title' => 'Expectation Failed',
		'message' => 'No expectation was seen, the Expect request header field was not presented by the client. Only the 100 Continue expectation is supported.',
	],
	421 => [
		'title' => 'Misdirected Request',
		'message' => 'The client needs a new connection for this request as the requested host name does not match the Server Name Indication (SNI) in use for this connection.',
	],
	422 => [
		'title' => 'Unprocessable Entity',
		'message' => 'The server understands the media type of the request entity, but was unable to process the contained instructions.',
	],
	423 => [
		'title' => 'Locked',
		'message' => 'The requested resource is currently locked. The lock must be released or proper identification given before the method can be applied.',
	],
	424 => [
		'title' => 'Failed Dependency',
		'message' => 'The method could not be performed on the resource because the requested action depended on another action and that other action failed.',
	],
	426 => [
		'title' => 'Upgrade Required',
		'message' => 'The requested resource can only be retrieved using SSL. The server is willing to upgrade the current connection to SSL, but your client doesn\'t support it. Either upgrade your client, or try requesting the page using HTTPS.',
	],
	428 => [
		'title' => 'Precondition Required',
		'message' => 'The request is required to be conditional.',
	],
	429 => [
		'title' => 'Too Many Requests',
		'message' => 'The user has sent too many requests in a given amount of time.',
	],
	431 => [
		'title' => 'Request Header Fields Too Large',
		'message' => 'The server refused this request because the request header fields are too large.',
	],
	451 => [
		'title' => 'Unavailable for Legal Reasons',
		'message' => 'Access to this URL has been denied for legal reasons.',
	],
	501 => [
		'title' => 'Not Implemented',
		'message' => 'The requested method is not supported for this URL.',
	],
	502 => [
		'title' => 'Bad Gateway',
		'message' => 'The proxy server received an invalid response from an upstream server.',
	],
	504 => [
		'title' => 'Gateway Timeout',
		'message' => 'The gateway did not receive a timely response from the upstream server or application.',
	],
	505 => [
		'title' => 'HTTP Version Not Supported',
		'message' => $server_error,
	],
	506 => [
		'title' => 'Variant Also Negotiates',
		'message' => 'A variant for the requested resource is itself a negotiable resource. This indicates a configuration error.',
	],
	507 => [
		'title' => 'Insufficient Storage',
		'message' => 'The method could not be performed on the resource because the server is unable to store the representation needed to successfully complete the request. There is insufficient free space left in your storage allocation.',
	],
	508 => [
		'title' => 'Loop Detected',
		'message' => 'The server terminated an operation because it encountered an infinite loop.',
	],
	510 => [
		'title' => 'Not Extended',
		'message' => 'A mandatory extension policy in the request is not accepted by the server for this resource.',
	],
	511 => [
		'title' => 'Network Authentication Required',
		'message' => 'The client needs to authenticate to gain network access.',
	],
];
