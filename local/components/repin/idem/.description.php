<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("TEST_NAME"),
	"DESCRIPTION" => GetMessage("TECT_DESC"),
	"ICON" => "",
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "service",
		"CHILD" => array(
			"ID" => "testing",
			"NAME" => GetMessage("TEST_TITLE")
		)
	),
);
?>