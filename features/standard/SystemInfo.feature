@systemInformation @IbexaOSS @IbexaContent @IbexaExperience @IbexaCommerce
Feature: System info verification
  As an administrator
  In order to customize my website
  I want to have access to all System Information.

  Background:
    Given I am logged as admin
    And I open "System Information" page in admin SiteAccess

  @javascript
  Scenario: Check My Ibexa Information
    When I go to "My Ibexa" tab in System Information
    Then I see "Product" system information table

  @javascript
  Scenario: Check Composer System Information
    When I go to "Composer" tab in System Information
    Then I see "Composer" system information table
      And I see listed packages
        | Name                                    |
        | ezsystems/ez-support-tools              |
        | ezsystems/ezplatform-admin-ui           |
        | ezsystems/ezplatform-admin-ui-assets    |
        | ezsystems/ezplatform-design-engine      |
        | ezsystems/ezplatform-http-cache         |
        | ezsystems/ezplatform-solr-search-engine |
        | ezsystems/ezplatform-kernel             |
        | ezsystems/ezplatform-content-forms      |

  @javascript
  Scenario: Check Repository System Information
    When I go to "Repository" tab in System Information
    Then I see "Repository" system information table

  @javascript
  Scenario: Check Hardware System Information
    When I go to "Hardware" tab in System Information
    Then I see "Hardware" system information table

  @javascript
  Scenario: Check PHP System Information
    When I go to "PHP" tab in System Information
    Then I see "PHP" system information table

  @javascript
  Scenario: Check Symfony Kernel System Information
    When I go to "Symfony Kernel" tab in System Information
    Then I see "Symfony Kernel" system information table
      And I see listed bundles
        | Name                                      |
        | EzPlatformAdminUiAssetsBundle             |
        | EzPlatformAdminUiBundle                   |
        | EzPlatformDesignEngineBundle              |
        | EzPublishCoreBundle                       |
        | EzPublishIOBundle                         |
        | EzPublishLegacySearchEngineBundle         |
        | EzPlatformRestBundle                      |
        | EzSystemsEzPlatformSolrSearchEngineBundle |
        | EzSystemsEzSupportToolsBundle             |
        | EzSystemsPlatformHttpCacheBundle          |
        | EzSystemsPlatformInstallerBundle          |
        | EzPlatformContentFormsBundle              |
