Feature: Content items creation
  As an administrator
  In order to menage content to my site
  I want to create, edit, copy and move content items.
  
Background:
  Given I am logged as "admin"
    And I go to "Content structure" in "Content" tab

@javascript @common
Scenario: Content moving can be cancelled
  Given I start creating a new content "Article"
    And I set content fields
      | label | value               |
      | Title | Test Article Manage |
    And I set article main content field to "Test article manage intro"
    And I click on the edit action bar button "Publish"
    And success notification that "Content published." appears
    And I should be on content container page "Test Article Manage" of type "Article" in root path
  When I click on the edit action bar button "Move"
    And I select content "Media/Files" through UDW
    And I close the UDW window
  Then I should be on content container page "Test Article Manage" of type "Article" in root path
    And content attributes equal
      | label | value               |
      | Title | Test Article Manage |
    And article main content field equals "Test article manage intro"
    And breadcrumb shows "Test Article Manage" path under root path

@javascript @common
Scenario: Content can be moved
  Given I navigate to content "Test Article Manage" of type "Article" in root path
  When I click on the edit action bar button "Move"
    And I select content "Media/Files" through UDW
    And I confirm the selection in UDW
  Then success notification that "'Test Article Manage' moved to 'Files'" appears
    And I should be on content container page "Test Article Manage" of type "Article" in "Media/Files"
    And content attributes equal
      | label | value               |
      | Title | Test Article Manage |
    And article main content field equals "Test article manage intro"
    And breadcrumb shows "Media/Files/Test Article Manage" path
    And going to root path there is no "Test Article Manage" "Article" on Sub-items list

@javascript @common
Scenario: Content copying can be cancelled
  Given I navigate to content "Test Article Manage" of type "Article" in "Media/Files"
  When I click on the edit action bar button "Copy"
    And I select content root node through UDW
    And I close the UDW window
  Then I should be on content container page "Test Article Manage" of type "Article" in "Media/Files"
    And content attributes equal
      | label | value               |
      | Title | Test Article Manage |
    And article main content field equals "Test article manage intro"
    And going to root path there is no "Test Article Manage" "Article" on Sub-items list

@javascript @common
Scenario: Content can be copied
  Given I navigate to content "Test Article Manage" of type "Article" in "Media/Files"
  When I click on the edit action bar button "Copy"
    And I select content root node through UDW
    And I confirm the selection in UDW
  Then success notification that "Test Article Manage" has been copied to root node appears
    And I should be on content container page "Test Article Manage" of type "Article" in root path
    And content attributes equal
      | label | value               |
      | Title | Test Article Manage |
    And article main content field equals "Test article manage intro"
    And going to "Media/Files" there is a "Test Article Manage" "Article" on Sub-items list

@javascript @common
Scenario: Subtree copying can be cancelled
  Given I navigate to content "Files" of type "Folder" in "Media"
  When I click on the edit action bar button "Copy Subtree"
    And I select content "Media/Images" through UDW
    And I close the UDW window
  Then I should be on content container page "Files" of type "Folder" in "Media"
    And going to "Media/Images" there is no "Files" "Folder" on Sub-items list

@javascript @common
Scenario: Subtree can be copied
  Given I navigate to content "Files" of type "Folder" in "Media"
  When I click on the edit action bar button "Copy Subtree"
    And I select content "Media/Images" through UDW
    And I confirm the selection in UDW
  Then success notification that "Subtree 'Files' copied to location 'Images'" appears
    And I should be on content container page "Files" of type "Folder" in "Media/Images"
    And there's "Test Article Manage" "Article" on "Files" Sub-items list

@javascript @common
Scenario: Content can be moved to trash from non-root location
  Given I navigate to content "Test Article Manage" of type "Article" in "Media/Files"
  When I send content to trash
  Then there's no "Article" "Test Article Manage" on "Files" Sub-items list
    And going to trash there is "Article" "Test Article Manage" on list

@javascript @common
Scenario: Content can be moved to trash from root location
  Given I open UDW and go to "root/Test Article Manage"
  When I send content to trash
  Then I should be redirected to root in default view
    And going to trash there is "Article" "Test Article Manage" on list

