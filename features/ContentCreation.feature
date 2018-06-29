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
  Then I should be on content container page "eZ Platform" of type "Folder"
    And there's no "Test Article" "Article" on "eZ Platform" Sub-items list

@javascript @common
Scenario: Content creation can be closed
  When I start creating a new content "Article"
    And I set content fields
      | label | value        |
      | Title | Test Article |
    And I click on the close button
  Then I should be on content container page "eZ Platform" of type "Folder"
    And there's no "Test Article" "Article" on "eZ Platform" Sub-items list
	
@javascript @common
Scenario: Content can be previewed during creation
  When I start creating a new content "Article"
    And I set content fields
      | label | value               |
      | Title | Test Article2       |
      | Intro | Test article2 intro |
    And I click on the edit action bar button "Preview"
    And I go to "tablet" view in "Test Article2" preview
    And I go to "mobile" view in "Test Article2" preview
    And I go to "desktop" view in "Test Article2" preview
    And I go back from content "Test Article2" preview
  Then I should be on "Content Update" "Test Article2" page
    And content fields are set
      | label | value               |
      | Title | Test Article2       |
      | Intro | Test article2 intro |

@javascript @common
Scenario: Content draft can be saved
  When I start creating a new content "Article"
    And I set content fields
      | label | value              |
      | Title | Test Article       |
      | Intro | Test article intro |
    And I click on the edit action bar button "Save"
  Then I should be on "Content Update" "Test Article" page
    And success notification that "Content draft saved." appears
    And going to dashboard we see there's draft "Test Article" on list

@javascript @common
Scenario: Content draft can be published
  Given going to dashboard we see there's draft "Test Article2" on list
  When I start editing content draft "Test Article2"
    And I click on the edit action bar button "Publish"
  Then I should be on content container page "Test Article2" of type "Article" in "eZ Platform"
    And going to dashboard we see there's no draft "Test Article2" on list

@javascript @common
Scenario: Content draft can be deleted
  Given going to dashboard we see there's draft "Test Article" on list
  When I start editing content draft "Test Article"
    And I set content fields
      | label | value                     |
      | Title | Test Article edited       |
      | Intro | Test article intro edited |
    And I click on the edit action bar button "Delete draft"
  Then I should be on content container page "eZ Platform" of type "Folder"
    And going to dashboard we see there's no draft "Test Article edited" on list

@javascript @common
Scenario: Content can be published
  When I start creating a new content "Article"
    And I set content fields
      | label | value              |
      | Title | Test Article       |
      | Intro | Test article intro |
    And I click on the edit action bar button "Publish"
  Then I should be on content container page "Test Article" of type "Article" in "eZ Platform"
    And success notification that "Content published." appears
    And content attributes equal
      | label | value              |
      | Title | Test Article       |
      | Intro | Test article intro |

@javascript @common
Scenario: Content edit draft can be deleted
  Given I navigate to content "Test Article" of type "Article" in "Home"
  When I click on the edit action bar button "Edit"
    And I set content fields
      | label | value                     |
      | Title | Test Article edited       |
      | Intro | Test article intro edited |
    And I click on the edit action bar button "Delete draft"
  Then I should be on content container page "Test Article" of type "Article" in "eZ Platform"
    And going to dashboard we see there's no draft "Test Article edited" on list

@javascript @common
Scenario: Content draft can be edited from dashboard
  Given I navigate to content "Test Article" of type "Article" in "Home"
    And I click on the edit action bar button "Edit"
    And I set content fields
      | label | value                     |
      | Title | Test Article edited       |
      | Intro | Test article intro edited |
    And I click on the edit action bar button "Save"
    And I click on the close button
    And going to dashboard we see there's draft "Test Article edited" on list
  When I start editing content draft "Test Article edited"
  Then I should be on "Content Update" "Test Article edited" page

@javascript @common
Scenario: Content draft edition can be closed
  Given going to dashboard we see there's draft "Test Article edited" on list
  When I start editing content draft "Test Article edited"
    And I set content fields
      | label | value                      |
      | Title | Test Article edited2       |
      | Intro | Test article intro edited2 |
    And I click on the close button
  Then I should be on content container page "Test Article" of type "Article" in "eZ Platform"

@javascript @common
Scenario: Content edit draft can be saved
  Given going to dashboard we see there's draft "Test Article edited" on list
  When I start editing content draft "Test Article edited"
    And I set content fields
      | label | value                      |
      | Title | Test Article edited2       |
      | Intro | Test article intro edited2 |
    And I click on the edit action bar button "Save"
  Then I should be on "Content Update" "Test Article edited2" page
    And success notification that "Content draft saved." appears
    And going to dashboard we see there's draft "Test Article edited2" on list

@javascript @common
Scenario: Content draft can be created and published through draft list modal
  Given I navigate to content "Test Article" of type "Article" in "Home"
  When I click on the edit action bar button "Edit"
    And I start creating new draft from draft conflict modal
    And I set content fields
      | label | value                      |
      | Title | Test Article edited3       |
    And I click on the edit action bar button "Publish"
  Then I should be on content container page "Test Article edited3" of type "Article" in "eZ Platform"
    And success notification that "Content published." appears
    And content attributes equal
      | label | value                |
      | Title | Test Article edited3 |
      | Intro | Test article intro   |

@javascript @common
Scenario: Content can be previewed during edition
  Given I navigate to content "Test Article edited3" of type "Article" in "Home"
  When I click on the edit action bar button "Edit"
    And I click on the edit action bar button "Preview"
    And I go to "tablet" view in "Test Article edited3" preview
    And I go to "mobile" view in "Test Article edited3" preview
    And I go to "desktop" view in "Test Article edited3" preview
    And I go back from content "Test Article edited3" preview
  Then I should be on "Content Update" "Test Article edited3" page
    And content fields are set
      | label | value                |
      | Title | Test Article edited3 |
      | Intro | Test article intro   |

@javascript @common
Scenario: Content draft from draft list modal can be published
  Given I navigate to content "Test Article edited3" of type "Article" in "Home"
  When I click on the edit action bar button "Edit"
    And I start editing draft with ID "4" from draft conflict modal
    And I set content fields
      | label | value                      |
      | Title | Test Article edited4       |
    And I click on the edit action bar button "Publish"
  Then I should be on content container page "Test Article edited4" of type "Article" in "eZ Platform"
    And success notification that "Content published." appears
    And content attributes equal
      | label | value                |
      | Title | Test Article edited4 |
      | Intro | Test article intro   |

@javascript @common
Scenario: Content moving can be cancelled
  Given I navigate to content "Test Article edited4" of type "Article" in "Home"
  When I click on the edit action bar button "Move"
    And I select content "Media/Images" through UDW
    And I close the UDW window
  Then I should be on content container page "Test Article edited4" of type "Article" in "eZ Platform"
    And content attributes equal
      | label | value                |
      | Title | Test Article edited4 |
      | Intro | Test article intro   |
    And breadcrumb shows "eZ Platform/Test Article edited4" path

@javascript @common
Scenario: Content can be moved
  Given I navigate to content "Test Article edited4" of type "Article" in "Home"
  When I click on the edit action bar button "Move"
    And I select content "Media/Images" through UDW
    And I confirm the selection in UDW
  Then I should be on content container page "Test Article edited4" of type "Article" in "Media/Images"
    And success notification that "'Test Article edited4' moved to 'Images'" appears
    And content attributes equal
      | label | value                |
      | Title | Test Article edited4 |
      | Intro | Test article intro   |
    And breadcrumb shows "Media/Images/Test Article edited4" path
    And going to "Home" there is no "Test Article edited4" "Article" on Sub-items list

@javascript @common
Scenario: Content copying can be cancelled
  Given I navigate to content "Test Article edited4" of type "Article" in "Media/Images"
  When I click on the edit action bar button "Copy"
    And I select content "eZ Platform" through UDW
    And I close the UDW window
  Then I should be on content container page "Test Article edited4" of type "Article" in "Media/Images"
    And content attributes equal
      | label | value                |
      | Title | Test Article edited4 |
      | Intro | Test article intro   |
    And going to "Home" there is no "Test Article edited4" "Article" on Sub-items list

@javascript @common
Scenario: Content can be copied
  Given I navigate to content "Test Article edited4" of type "Article" in "Media/Images"
  When I click on the edit action bar button "Copy"
    And I select content "eZ Platform" through UDW
    And I confirm the selection in UDW
  Then I should be on content container page "Test Article edited4" of type "Article" in "eZ Platform"
    And success notification that "'Test Article edited4' copied to 'eZ Platform'" appears
    And content attributes equal
      | label | value                |
      | Title | Test Article edited4 |
      | Intro | Test article intro   |
    And going to "Media/Images" there is a "Test Article edited4" "Article" on Sub-items list

@javascript @common
Scenario Outline: Content can be moved to trash
  Given I navigate to content "<itemName>" of type "<itemType>" in "<path>"
  When I send content to trash
  Then there's no "<itemType>" "<itemName>" on "<folder>" Sub-items list
    And going to trash there is "<itemType>" "<itemName>" on list

  Examples:
    | itemName             | itemType | path         | folder |
    | Test Article edited4 | Article  | Media/Images | Images |
    | Test Article edited4 | Article  | Home         | Home   |
    | Test Article2        | Article  | Home         | Home   |
