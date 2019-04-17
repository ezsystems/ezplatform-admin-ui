Feature: Content items creation
  As an administrator
  In order to menage content to my site
  I want to create, edit, copy and move content items.
  
Background:
  Given I am logged as "admin"
    And I go to "Content structure" in "Content" tab
  
@javascript @common
Scenario: Content creation can be cancelled
  When I start creating a new content "Article"
    And I set content fields
      | label | value        |
      | Title | Test Article |
    And I click on the edit action bar button "Cancel"
  Then I should be on root container page in Content View
    And there's no "Test Article" "Article" on Sub-items list of root

@javascript @common
Scenario: Content creation can be closed
  When I start creating a new content "Article"
    And I set content fields
      | label | value        |
      | Title | Test Article |
    And I click on the close button
  Then I should be on root container page in Content View
    And there's no "Test Article" "Article" on Sub-items list of root

@javascript @common
Scenario: Content can be previewed during creation
  When I start creating a new content "Article"
    And I set content fields
      | label | value              |
      | Title | Test Article       |
    And I set article main content field to "Test article intro"
    And I click on the edit action bar button "Preview"
    And I go to "tablet" view in "Test Article" preview
    And I go to "mobile" view in "Test Article" preview
    And I go to "desktop" view in "Test Article" preview
    And I go back from content "Test Article" preview
  Then I should be on "Content Update" "Test Article" page
    And content fields are set
      | label | value        |
      | Title | Test Article |
    And article main content field is set to "Test article intro"

@javascript @common
Scenario: Content can be published
  When I start creating a new content "Article"
    And I set content fields
      | label | value        |
      | Title | Test Article |
    And I set article main content field to "Test article intro"
    And I click on the edit action bar button "Publish"
  Then success notification that "Content published." appears
    And I should be on content container page "Test Article" of type "Article" in root path
    And content attributes equal
      | label | value        |
      | Title | Test Article |
    And article main content field equals "Test article intro"

@javascript @common
Scenario: Content can be edited
  Given I navigate to content "Test Article" of type "Article" in root path
    And I click on the edit action bar button "Edit"
    And I set content fields
      | label | value               |
      | Title | Test Article edited |
    And I set article main content field to "Test Article intro edited"
    And I click on the edit action bar button "Publish"
  Then success notification that "Content published." appears
    And I should be on content container page "Test Article edited" of type "Article" in root path
    And content attributes equal
      | label | value               |
      | Title | Test Article edited |
    And article main content field equals "Test Article intro edited"

@javascript @common
Scenario: Content can be previewed during edition
  Given I navigate to content "Test Article edited" of type "Article" in root path
  When I click on the edit action bar button "Edit"
    And I should be on "Content Update" "Test Article edited" page
    And I click on the edit action bar button "Preview"
    And I go to "tablet" view in "Test Article edited" preview
    And I go to "mobile" view in "Test Article edited" preview
    And I go to "desktop" view in "Test Article edited" preview
    And I go back from content "Test Article edited" preview
  Then I should be on "Content Update" "Test Article edited" page
    And content fields are set
      | label | value               |
      | Title | Test Article edited |
    And article main content field is set to "Test Article intro edited"

@javascript @common
Scenario: Content moving can be cancelled
  Given I navigate to content "Test Article edited" of type "Article" in root path
  When I click on the edit action bar button "Move"
    And I select content "Media/Files" through UDW
    And I close the UDW window
  Then I should be on content container page "Test Article edited" of type "Article" in root path
    And content attributes equal
      | label | value               |
      | Title | Test Article edited |
    And article main content field equals "Test Article intro edited"
    And breadcrumb shows "Test Article edited" path under root path

@javascript @common
Scenario: Content can be moved
  Given I navigate to content "Test Article edited" of type "Article" in root path
  When I click on the edit action bar button "Move"
    And I select content "Media/Files" through UDW
    And I confirm the selection in UDW
  Then success notification that "'Test Article edited' moved to 'Files'" appears
    And I should be on content container page "Test Article edited" of type "Article" in "Media/Files"
    And content attributes equal
      | label | value               |
      | Title | Test Article edited |
    And article main content field equals "Test Article intro edited"
    And breadcrumb shows "Media/Files/Test Article edited" path
    And going to root path there is no "Test Article edited" "Article" on Sub-items list

@javascript @common
Scenario: Content copying can be cancelled
  Given I navigate to content "Test Article edited" of type "Article" in "Media/Files"
  When I click on the edit action bar button "Copy"
    And I select content root node through UDW
    And I close the UDW window
  Then I should be on content container page "Test Article edited" of type "Article" in "Media/Files"
    And content attributes equal
      | label | value               |
      | Title | Test Article edited |
    And article main content field equals "Test Article intro edited"
    And going to root path there is no "Test Article edited" "Article" on Sub-items list

@javascript @common
Scenario: Content can be copied
  Given I navigate to content "Test Article edited" of type "Article" in "Media/Files"
  When I click on the edit action bar button "Copy"
    And I select content root node through UDW
    And I confirm the selection in UDW
  Then success notification that "Test Article edited" has been copied to root node appears
    And I should be on content container page "Test Article edited" of type "Article" in root path
    And content attributes equal
      | label | value                |
      | Title | Test Article edited |
    And article main content field equals "Test Article intro edited"
    And going to "Media/Files" there is a "Test Article edited" "Article" on Sub-items list

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
    And there's "Test Article edited" "Article" on "Files" Sub-items list

@javascript @common
Scenario: Content can be moved to trash from non-root location
  Given I navigate to content "Test Article edited" of type "Article" in "Media/Files"
  When I send content to trash
  Then there's no "Article" "Test Article edited" on "Files" Sub-items list
    And going to trash there is "Article" "Test Article edited" on list

@javascript @common
Scenario: Content can be moved to trash from root location
  Given I navigate to content "Test Article edited" of type "Article" in root path
  When I send content to trash
  Then I should be redirected to root in default view
    And going to trash there is "Article" "Test Article edited" on list

