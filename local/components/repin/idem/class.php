<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;
use Bitrix\Main\Loader;

class CTest extends CBitrixComponent
{


	public function onPrepareComponentParams($arParams)
    {
		$result = array(
            'IBLOCK_TYPE' => trim($arParams['IBLOCK_TYPE']),
            'IBLOCK_ID' => intval($arParams['IBLOCK_ID']),
            'CACHE_TIME' => intval($arParams['CACHE_TIME']) > 0 ? intval($arParams['CACHE_TIME']) : 3600,
            'CACHE_TYPE' => $arParams['CACHE_TYPE'] === "Y" ? "Y" : "N",
        );
        return $result;
    }


	public function onIncludeComponentLang()
    {
        Loc::loadMessages(__FILE__);
    }


	protected function checkModules()
    {
        if (!Loader::includeModule('iblock'))
            throw new SystemException(Loc::getMessage('IBLOCK_MODULE_NOT_INSTALLED'));
    }

	public function executeComponent()
    {
        try {
            $this->checkModules();

			if ( ($this->arParams["CACHE_TYPE"] === "N") || ($this->arParams["CACHE_TYPE"] === "Y" && $this->startResultCache())) {

				// разделы
				$rsSection = \Bitrix\Iblock\SectionTable::getList([
					'order' => ['LEFT_MARGIN'=>'ASC'],
					'filter' => [
						'IBLOCK_ID' => $this->arParams["IBLOCK_ID"],
						'ACTIVE' => 'Y',
						'GLOBAL_ACTIVE' => 'Y',
					], 
					'select' => [
						'ID',
						'NAME',
						'DEPTH_LEVEL',
					],
				]);
				$arSection = [];
				while ($arItem=$rsSection->fetch()) 
				{
					$arSection[$arItem["ID"]] = $arItem;
				}
				// id свойства
				$resultObj = \Bitrix\Iblock\PropertyTable::getList([
					'select' => ['ID'],
					'filter' => [
							'IBLOCK_ID' => $this->arParams["IBLOCK_ID"],
							'CODE' => "TAGS",
					], 
				]);
				$propertyObj = $resultObj->fetch();
				$iblockPropertyId = $propertyObj["ID"];
				// элементы
				$items = [];
				$iblock = \Bitrix\Iblock\Iblock::wakeUp($this->arParams["IBLOCK_ID"]);
				$rsElements = $iblock->getEntityDataClass()::getList([
					'select' => ['ID', 'NAME', 'SECTIONS', "TAGS"],
				])->fetchCollection();
				foreach ($rsElements as $element) 
				{
					$valueArray = [];
					$resultObj = \Bitrix\Iblock\ElementPropertyTable::getList([
						'select' => ['VALUE'],
						'filter' => [
								'IBLOCK_ELEMENT_ID' => $element->get("ID"),
								'IBLOCK_PROPERTY_ID' => $iblockPropertyId,
						], 
					]);
					while ($rowArray = $resultObj->fetch()) {
						$valueArray[] = $rowArray['VALUE'];
					}
					$item = [
						"ID" => $element->get("ID"),
						"NAME" => $element->get("NAME"),
						"TAGS" => $valueArray,
					];
					// ищем привязку к разделам
					$inSection = false;
					foreach ($element->getSections()->getAll() as $section) {
						$idSection = $section->getId();
						if(isset($arSection[$idSection])) {
							$arSection[$idSection]["ITEMS"][] = $item;
							$inSection = true;
						}
					}
					if(!$inSection) {
						// не входит ни в один раздел
						$items[] = $item;
					}
				}
				$this->arResult["SECTION"] = $arSection;
				$this->arResult["ITEMS"] = $items;

				if (isset($this->arResult)) {
					if($this->arParams["CACHE_TYPE"] === "Y") {
						$this->SetResultCacheKeys(
							array()
						);
					}
					// подключаем шаблон и сохраняем кеш
					$this->IncludeComponentTemplate();
				} else { // данных нет, прерываем кеширование
					$this->AbortResultCache();
				}
			}
	
        } catch (SystemException $e) {
            ShowError($e->getMessage());
        }
    }

}


