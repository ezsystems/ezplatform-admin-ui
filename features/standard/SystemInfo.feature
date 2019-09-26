Feature: System info verification
  As an administrator
  In order to customize my eZ installation
  I want to have access to all System Information.

  Background:
    Given I am logged as "admin"
      And I go to "System Information" in "Admin" tab

  @javascript @common
  Scenario: Check Composer System Information
    When I go to "Composer" tab in System Information
    Then I see "Composer" system information table
      And I see "Packages" table with given records
        | Name                                    |
        | ezsystems/ez-support-tools              |
        | ezsystems/ezplatform-admin-ui           |
        | ezsystems/ezplatform-admin-ui-assets    |
        | ezsystems/ezplatform-admin-ui-modules   |
        | ezsystems/ezplatform-design-engine      |
#        | ezsystems/ezplatform-http-cache         | # @todo Uncomment once this package is supported in eZ Platform 3
        | ezsystems/ezplatform-solr-search-engine |
        | ezsystems/ezpublish-kernel              |
        | ezsystems/repository-forms              |

  @javascript @common
  Scenario: Check Database System Information
    When I go to "Database" tab in System Information
    Then I see "Database" system information table

  @javascript @common
  Scenario: Check Hardware System Information
    When I go to "Hardware" tab in System Information
    Then I see "Hardware" system information table

  @javascript @common
  Scenario: Check PHP System Information
    When I go to "PHP" tab in System Information
    Then I see "PHP" system information table

  @javascript @common
  Scenario: Check Symfony Kernel System Information
    When I go to "Symfony Kernel" tab in System Information
    Then I see "Symfony Kernel" system information table
      And I see "Bundles" table with given records
        | Name                                      |
        | EzPlatformAdminUiAssetsBundle             |
        | EzPlatformAdminUiBundle                   |
        | EzPlatformAdminUiModulesBundle            |
        | EzPlatformDesignEngineBundle              |
        | EzPublishCoreBundle                       |
        | EzPublishIOBundle                         |
        | EzPublishLegacySearchEngineBundle         |
        | EzPlatformRestBundle                      |
        | EzSystemsEzPlatformSolrSearchEngineBundle |
        | EzSystemsEzSupportToolsBundle             |
#        | EzSystemsPlatformHttpCacheBundle          | # @todo Uncomment once this bundle is supported in eZ Platform 3
        | EzSystemsPlatformInstallerBundle          |
        | EzSystemsRepositoryFormsBundle            |
