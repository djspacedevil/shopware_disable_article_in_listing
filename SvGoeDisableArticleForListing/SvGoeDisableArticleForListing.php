<?php

namespace SvGoeDisableArticleForListing;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;

class SvGoeDisableArticleForListing extends Plugin {

    /** Install Module
     * @param InstallContext $context
     */
    public function install(InstallContext $context) {

        $attributeService = $this->container->get('shopware_attribute.crud_service');
        $attributeService ->update('s_articles_attributes', 'sv_goe_hide_in_listing', 'boolean', [
            'label' => 'Artikel im Listing verstecken?',
            'supportText' => 'Soll der Artikel in der Übersicht ausgeblendet werden? (JA/NEIN))',
            'helpText' => '',
            'translatable' => false,
            'displayInBackend' => true,
            'entity' => 'Shopware\Models\Article\Article',
            'position' => 1000,
            'custom' => false
        ]);

        $this->rebuildAttr();
        $context->scheduleClearCache($this->clearCache());
        parent::install($context);
    }

    /** Uninstall Module
     * @param UninstallContext $context
     */
    public function uninstall(UninstallContext $context)
    {
        $context->scheduleClearCache($this->clearCache());
        parent::uninstall($context);
    }

    /** Subscribe Module Events
     * @return array
     */
    public static function getSubscribedEvents() {
        return ['Enlight_Controller_Action_PostDispatch_Frontend_Listing' => 'onFrontendListing'];
    }

    /** Change Frontend Infos, Remove Articles that are flagged.
     * @param \Enlight_Event_EventArgs $args
     * @void
     */
    public function onFrontendListing(\Enlight_Event_EventArgs $args) {

        //Methode 1 to remove the complete article infos from listing, but keep it in topseller list
        //If this is to use, remove the template files from Resources and commented the methode 2 lines
        /*
        $view = $args->get('subject')->View();
        $sArticles = $view->getAssign('sArticles');

        foreach ($sArticles as $key=>$sArticle) {
            if (isset($sArticle['sv_goe_hide_in_listing']) && (bool)$sArticle['sv_goe_hide_in_listing']) {
                unset($sArticles[$key]);
            }
        }
        $view->assign('sArticles', $sArticles);
        */

        //Methode 2 to remove the article infos from listing per template
        $this->container->get('Template')->addTemplateDir($this->getPath() . '/Resources/views/');

    }

    /** Clear Cache Function
     * @return array InstallContext CACHE_TAG_CONFIG, CACHE_TAG_HTTP
     */
    private function clearCache() {
        return [
            InstallContext::CACHE_TAG_CONFIG,
            InstallContext::CACHE_TAG_HTTP
        ];
    }

    /**
     * Rebuild Attributes, sometimes is it needed...
     * @void
     */
    private function rebuildAttr() {
        $metaDataCache = Shopware()->Models()->getConfiguration()->getMetadataCacheImpl();
        $metaDataCache->deleteAll();
        Shopware()->Models()->generateAttributeModels(['s_articles_attributes']);
    }
}