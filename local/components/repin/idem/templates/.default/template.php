<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;

Loc::loadMessages(__FILE__);


?>
<div>
	<?foreach($arResult["SECTION"] as $keySec => $itemSec) {?>
		<?if(!isset($itemSec["ITEMS"]) || count($itemSec["ITEMS"]) == 0) continue;?>

		<p class="blue"><?=$itemSec["NAME"]?></p>
		<?foreach($itemSec["ITEMS"] as $itemElm) {?>
			<p class="green">- <?=$itemElm["NAME"]?></p>
			<?if(count($itemElm["TAGS"]) > 0) {?>
				<p class="red"> Теги: <?=implode(",", $itemElm["TAGS"])?></p>
			<?}?>
		<?}?>
	<?}?>

	<?foreach($arResult["ITEMS"] as $key => $item) {?>
		<p class="green"> - <?=$item["NAME"]?></p>
		<?if(count($item["TAGS"]) > 0) {?>
			<p class="red"> Теги: <?=implode(",", $item["TAGS"])?></p>
		<?}?>
	<?}?>

</div>

