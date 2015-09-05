<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// Import the classes that we're going to be using
use EDAM\Types\Data, EDAM\Types\Note, EDAM\NoteStore\NoteFilter, EDAM\NoteStore\NotesMetadataResultSpec, EDAM\Types\Resource, EDAM\Types\ResourceAttributes, EDAM\Types\Tag;
use EDAM\Error\EDAMUserException, EDAM\Error\EDAMErrorCode;
use Evernote\Client;

require_once 'lib/autoload.php';

require_once 'lib/Evernote/Client.php';

require_once 'lib/packages/Errors/Errors_types.php';
require_once 'lib/packages/Types/Types_types.php';
require_once 'lib/packages/Limits/Limits_constants.php';

// A global exception handler for our program so that error messages all go to the console
function en_exception_handler($exception)
{
    echo "Uncaught " . get_class($exception) . ":\n";
    if ($exception instanceof EDAMUserException) {
        echo "Error code: " . EDAMErrorCode::$__names[$exception->errorCode] . "\n";
        echo "Parameter: " . $exception->parameter . "\n";
    } elseif ($exception instanceof EDAMSystemException) {
        echo "Error code: " . EDAMErrorCode::$__names[$exception->errorCode] . "\n";
        echo "Message: " . $exception->message . "\n";
    } else {
        echo $exception;
    }
}
set_exception_handler('en_exception_handler');

// Real applications authenticate with Evernote using OAuth, but for the
// purpose of exploring the API, you can get a developer token that allows
// you to access your own Evernote account. To get a developer token, visit
// https://sandbox.evernote.com/api/DeveloperToken.action
$authToken = "";

if ($authToken == "your developer token") {
    print "Please fill in your developer token\n";
    print "To get a developer token, visit https://sandbox.evernote.com/api/DeveloperToken.action\n";
    exit(1);
}

// Initial development is performed on our sandbox server. To use the production
// service, change "sandbox.evernote.com" to "www.evernote.com" and replace your
// developer token above with a token from
// https://www.evernote.com/api/DeveloperToken.action
$client = new Client(array('token' => $authToken, 'sandbox' => false));

$userStore = $client->getUserStore();

// Connect to the service and check the protocol version
$versionOK =
    $userStore->checkVersion("Evernote EDAMTest (PHP)",
         $GLOBALS['EDAM_UserStore_UserStore_CONSTANTS']['EDAM_VERSION_MAJOR'],
         $GLOBALS['EDAM_UserStore_UserStore_CONSTANTS']['EDAM_VERSION_MINOR']);
print "Is my Evernote API version up to date?  " . $versionOK . "\n\n";
if ($versionOK == 0) {
    exit(1);
}

$noteStore = $client->getNoteStore();
$wptexturize = remove_filter( 'the_title', 'wptexturize' );
$postTit = get_the_title( $post_id );
if ( $wptexturize )
    add_filter( 'the_title', 'wptexturize' );
$search = new NoteFilter();
	$search->words = 'intitle:"'.$postTit.'"';
$spec = new NotesMetadataResultSpec();
	$spec->includeTitle = true;
	$spec->includeAttributes = true;
	$spec->includeNotebookGuid = true;
$result = $noteStore->findNotesMetadata($authToken,$search,0,3,$spec);
$notes = $result->notes;

if(count($notes) > 0)
foreach ($notes as $note) {
	similar_text($note->title, $postTit, $per);
	if ( $per > 95 ) {
		$guid = $note->guid;
		$tags = $noteStore->getNoteTagNames($guid);
		$sourceURL = $note->attributes->sourceURL;
		$bookGUID = $note->notebookGuid;
		foreach( $tags as $tag ){
			@$atag = $tag.",".$atag;
		}
		@$atag = rtrim($atag, ',');
		$book = $noteStore->getNotebook($bookGUID);
		$bookname = $book->name;
		$stack = $book->stack;

		if ($eveDeb) {
			$fh = fopen("$snoopy_palace/snoopy-evernote.log","a");
			$today = date("Y-m-d");
			$snoopy_evernote = $today.": guid: ".$guid."\ntitle: ".$note->title."\ntags: ".$atag."\nsourceURL: ".$sourceURL."\nbookGUID: ".$bookGUID."\nbookname: ".$bookname."\nstack: ".$stack;
			print $today.": guid: ".$guid."\ntitle: ".$note->title."\ntags: ".$atag."\nsourceURL: ".$sourceURL."\nbookGUID: ".$bookGUID."\nbookname: ".$bookname."\nstack: ".$stack;
			fwrite($fh, "$snoopy_evernote");
			`echo "post title: $postTit" >> $snoopy_palace/snoopy-evernote.log`;
		}

	$snoopy_evernote = true;
	break;
	}
}
?>