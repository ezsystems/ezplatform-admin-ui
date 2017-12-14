(function () {
    const listContainers = [...document.querySelectorAll('.ez-sil')];
    const mfuContainer = document.querySelector('#ez-mfu');
    const token = document.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = document.querySelector('meta[name="SiteAccess"]').content;
    const sortContainer = document.querySelector('[data-sort-field][data-sort-order]');
    const sortField = sortContainer.getAttribute('data-sort-field');
    const sortOrder = sortContainer.getAttribute('data-sort-order');
    const mfuAttrs = {
        adminUiConfig: Object.assign({}, window.eZ.adminUiConfig, {
            token,
            siteaccess
        }),
        parentInfo: {
            contentTypeIdentifier: mfuContainer.dataset.parentContentTypeIdentifier,
            contentTypeId: mfuContainer.dataset.parentContentTypeId,
            locationPath: mfuContainer.dataset.parentLocationPath,
            language: mfuContainer.dataset.parentContentLanguage
        },
    };

    const items = [
        {
            "location": {
                "_media-type": "application/vnd.ez.api.Location+json",
                "_href": "/api/ezp/v2/content/locations/1/2/91",
                "id": 91,
                "priority": 0,
                "hidden": false,
                "invisible": false,
                "ParentLocation": {
                    "_media-type": "application/vnd.ez.api.Location+json",
                    "_href": "/api/ezp/v2/content/locations/1/2"
                },
                "pathString": "/1/2/91/",
                "depth": 2,
                "childCount": 0,
                "remoteId": "9612fbcda874f5e8616d09067d6d4f5a",
                "Children": {
                    "_media-type": "application/vnd.ez.api.LocationList+json",
                    "_href": "/api/ezp/v2/content/locations/1/2/91/children"
                },
                "Content": {
                    "_media-type": "application/vnd.ez.api.Content+json",
                    "_href": "/api/ezp/v2/content/objects/89"
                },
                "sortField": "NAME",
                "sortOrder": "ASC",
                "UrlAliases": {
                    "_media-type": "application/vnd.ez.api.UrlAliasRefList+json",
                    "_href": "/api/ezp/v2/content/locations/1/2/91/urlaliases"
                },
                "ContentInfo": {
                    "_media-type": "application/vnd.ez.api.ContentInfo+json",
                    "_href": "/api/ezp/v2/content/objects/89",
                    "Content": {
                        "_media-type": "application/vnd.ez.api.ContentInfo+json",
                        "_href": "/api/ezp/v2/content/objects/89",
                        "_remoteId": "2ff2a034a4ff1daef16c1fc0d78bb04e",
                        "_id": 89,
                        "ContentType": {
                            "_media-type": "application/vnd.ez.api.ContentType+json",
                            "_href": "/api/ezp/v2/content/types/21"
                        },
                        "Name": "Mondadori",
                        "Versions": {
                            "_media-type": "application/vnd.ez.api.VersionList+json",
                            "_href": "/api/ezp/v2/content/objects/89/versions"
                        },
                        "CurrentVersion": {
                            "_media-type": "application/vnd.ez.api.Version+json",
                            "_href": "/api/ezp/v2/content/objects/89/currentversion"
                        },
                        "Section": {
                            "_media-type": "application/vnd.ez.api.Section+json",
                            "_href": "/api/ezp/v2/content/sections/1"
                        },
                        "Locations": {
                            "_media-type": "application/vnd.ez.api.LocationList+json",
                            "_href": "/api/ezp/v2/content/objects/89/locations"
                        },
                        "Owner": {
                            "_media-type": "application/vnd.ez.api.User+json",
                            "_href": "/api/ezp/v2/user/users/14"
                        },
                        "lastModificationDate": "2017-12-08T11:53:48+01:00",
                        "publishedDate": "2017-12-08T11:53:48+01:00",
                        "mainLanguageCode": "eng-GB",
                        "currentVersionNo": 1,
                        "alwaysAvailable": false,
                        "ObjectStates": {
                            "_media-type": "application/vnd.ez.api.ContentObjectStates+json",
                            "_href": "/api/ezp/v2/content/objects/89/objectstates"
                        }
                    }
                }
            },
            "content": {
                "_media-type": "application/vnd.ez.api.Content+json",
                "_href": "/api/ezp/v2/content/objects/89",
                "_remoteId": "2ff2a034a4ff1daef16c1fc0d78bb04e",
                "_id": 89,
                "ContentType": {
                    "_media-type": "application/vnd.ez.api.ContentType+json",
                    "_href": "/api/ezp/v2/content/types/21"
                },
                "Name": "Mondadori",
                "Versions": {
                    "_media-type": "application/vnd.ez.api.VersionList+json",
                    "_href": "/api/ezp/v2/content/objects/89/versions"
                },
                "CurrentVersion": {
                    "_media-type": "application/vnd.ez.api.Version+json",
                    "_href": "/api/ezp/v2/content/objects/89/currentversion",
                    "Version": {
                        "_media-type": "application/vnd.ez.api.Version+json",
                        "_href": "/api/ezp/v2/content/objects/89/versions/1",
                        "VersionInfo": {
                            "id": 589,
                            "versionNo": 1,
                            "status": "PUBLISHED",
                            "modificationDate": "2017-12-08T11:53:48+01:00",
                            "Creator": {
                                "_media-type": "application/vnd.ez.api.User+json",
                                "_href": "/api/ezp/v2/user/users/14"
                            },
                            "creationDate": "2017-12-08T11:53:48+01:00",
                            "initialLanguageCode": "eng-GB",
                            "languageCodes": "eng-GB",
                            "VersionTranslationInfo": {
                                "_media-type": "application/vnd.ez.api.VersionTranslationInfo+json",
                                "Language": [
                                    {
                                        "languageCode": "eng-GB"
                                    }
                                ]
                            },
                            "names": {
                                "value": [
                                    {
                                        "_languageCode": "eng-GB",
                                        "#text": "Mondadori"
                                    }
                                ]
                            },
                            "Content": {
                                "_media-type": "application/vnd.ez.api.ContentInfo+json",
                                "_href": "/api/ezp/v2/content/objects/89"
                            }
                        },
                        "Fields": {
                            "field": [
                                {
                                    "id": 295,
                                    "fieldDefinitionIdentifier": "new_ezobjectrelationlist_1",
                                    "languageCode": "eng-GB",
                                    "fieldTypeIdentifier": "ezobjectrelationlist",
                                    "fieldValue": {
                                        "destinationContentIds": [
                                            64
                                        ],
                                        "destinationContentHrefs": [
                                            "/api/ezp/v2/content/objects/64"
                                        ]
                                    }
                                },
                                {
                                    "id": 380,
                                    "fieldDefinitionIdentifier": "new_ezobjectrelation_2",
                                    "languageCode": "eng-GB",
                                    "fieldTypeIdentifier": "ezobjectrelation",
                                    "fieldValue": {
                                        "destinationContentId": null
                                    }
                                }
                            ]
                        },
                        "Relations": {
                            "_media-type": "application/vnd.ez.api.RelationList+json",
                            "_href": "/api/ezp/v2/content/objects/89/versions/1/relations",
                            "Relation": [
                                {
                                    "_media-type": "application/vnd.ez.api.Relation+json",
                                    "_href": "/api/ezp/v2/content/objects/89/versions/1/relations/1",
                                    "SourceContent": {
                                        "_media-type": "application/vnd.ez.api.ContentInfo+json",
                                        "_href": "/api/ezp/v2/content/objects/89"
                                    },
                                    "DestinationContent": {
                                        "_media-type": "application/vnd.ez.api.ContentInfo+json",
                                        "_href": "/api/ezp/v2/content/objects/64"
                                    },
                                    "SourceFieldDefinitionIdentifier": "new_ezobjectrelationlist_1",
                                    "RelationType": "ATTRIBUTE"
                                }
                            ]
                        }
                    }
                },
                "Section": {
                    "_media-type": "application/vnd.ez.api.Section+json",
                    "_href": "/api/ezp/v2/content/sections/1"
                },
                "MainLocation": {
                    "_media-type": "application/vnd.ez.api.Location+json",
                    "_href": "/api/ezp/v2/content/locations/1/2/91"
                },
                "Locations": {
                    "_media-type": "application/vnd.ez.api.LocationList+json",
                    "_href": "/api/ezp/v2/content/objects/89/locations"
                },
                "Owner": {
                    "_media-type": "application/vnd.ez.api.User+json",
                    "_href": "/api/ezp/v2/user/users/14"
                },
                "lastModificationDate": "2017-12-08T11:53:48+01:00",
                "publishedDate": "2017-12-08T11:53:48+01:00",
                "mainLanguageCode": "eng-GB",
                "currentVersionNo": 1,
                "alwaysAvailable": false,
                "ObjectStates": {
                    "_media-type": "application/vnd.ez.api.ContentObjectStates+json",
                    "_href": "/api/ezp/v2/content/objects/89/objectstates"
                }
            }
        },
        {
            "location": {
                "_media-type": "application/vnd.ez.api.Location+json",
                "_href": "/api/ezp/v2/content/locations/1/2/120",
                "id": 120,
                "priority": 0,
                "hidden": false,
                "invisible": false,
                "ParentLocation": {
                    "_media-type": "application/vnd.ez.api.Location+json",
                    "_href": "/api/ezp/v2/content/locations/1/2"
                },
                "pathString": "/1/2/120/",
                "depth": 2,
                "childCount": 0,
                "remoteId": "7e5aae453c2f0e5d4cd0305a9a685dad",
                "Children": {
                    "_media-type": "application/vnd.ez.api.LocationList+json",
                    "_href": "/api/ezp/v2/content/locations/1/2/120/children"
                },
                "Content": {
                    "_media-type": "application/vnd.ez.api.Content+json",
                    "_href": "/api/ezp/v2/content/objects/118"
                },
                "sortField": "NAME",
                "sortOrder": "ASC",
                "UrlAliases": {
                    "_media-type": "application/vnd.ez.api.UrlAliasRefList+json",
                    "_href": "/api/ezp/v2/content/locations/1/2/120/urlaliases"
                },
                "ContentInfo": {
                    "_media-type": "application/vnd.ez.api.ContentInfo+json",
                    "_href": "/api/ezp/v2/content/objects/118",
                    "Content": {
                        "_media-type": "application/vnd.ez.api.ContentInfo+json",
                        "_href": "/api/ezp/v2/content/objects/118",
                        "_remoteId": "8563f34c1cf542b4e9bb7f3b6b6d925c",
                        "_id": 118,
                        "ContentType": {
                            "_media-type": "application/vnd.ez.api.ContentType+json",
                            "_href": "/api/ezp/v2/content/types/18"
                        },
                        "Name": "Test blog post",
                        "Versions": {
                            "_media-type": "application/vnd.ez.api.VersionList+json",
                            "_href": "/api/ezp/v2/content/objects/118/versions"
                        },
                        "CurrentVersion": {
                            "_media-type": "application/vnd.ez.api.Version+json",
                            "_href": "/api/ezp/v2/content/objects/118/currentversion"
                        },
                        "Section": {
                            "_media-type": "application/vnd.ez.api.Section+json",
                            "_href": "/api/ezp/v2/content/sections/1"
                        },
                        "Locations": {
                            "_media-type": "application/vnd.ez.api.LocationList+json",
                            "_href": "/api/ezp/v2/content/objects/118/locations"
                        },
                        "Owner": {
                            "_media-type": "application/vnd.ez.api.User+json",
                            "_href": "/api/ezp/v2/user/users/14"
                        },
                        "lastModificationDate": "2017-12-11T13:34:48+01:00",
                        "publishedDate": "2017-12-11T13:34:48+01:00",
                        "mainLanguageCode": "eng-GB",
                        "currentVersionNo": 1,
                        "alwaysAvailable": false,
                        "ObjectStates": {
                            "_media-type": "application/vnd.ez.api.ContentObjectStates+json",
                            "_href": "/api/ezp/v2/content/objects/118/objectstates"
                        }
                    }
                }
            },
            "content": {
                "_media-type": "application/vnd.ez.api.Content+json",
                "_href": "/api/ezp/v2/content/objects/118",
                "_remoteId": "8563f34c1cf542b4e9bb7f3b6b6d925c",
                "_id": 118,
                "ContentType": {
                    "_media-type": "application/vnd.ez.api.ContentType+json",
                    "_href": "/api/ezp/v2/content/types/18"
                },
                "Name": "Test blog post",
                "Versions": {
                    "_media-type": "application/vnd.ez.api.VersionList+json",
                    "_href": "/api/ezp/v2/content/objects/118/versions"
                },
                "CurrentVersion": {
                    "_media-type": "application/vnd.ez.api.Version+json",
                    "_href": "/api/ezp/v2/content/objects/118/currentversion",
                    "Version": {
                        "_media-type": "application/vnd.ez.api.Version+json",
                        "_href": "/api/ezp/v2/content/objects/118/versions/1",
                        "VersionInfo": {
                            "id": 619,
                            "versionNo": 1,
                            "status": "PUBLISHED",
                            "modificationDate": "2017-12-11T13:34:48+01:00",
                            "Creator": {
                                "_media-type": "application/vnd.ez.api.User+json",
                                "_href": "/api/ezp/v2/user/users/14"
                            },
                            "creationDate": "2017-12-11T13:34:48+01:00",
                            "initialLanguageCode": "eng-GB",
                            "languageCodes": "eng-GB",
                            "VersionTranslationInfo": {
                                "_media-type": "application/vnd.ez.api.VersionTranslationInfo+json",
                                "Language": [
                                    {
                                        "languageCode": "eng-GB"
                                    }
                                ]
                            },
                            "names": {
                                "value": [
                                    {
                                        "_languageCode": "eng-GB",
                                        "#text": "Test blog post"
                                    }
                                ]
                            },
                            "Content": {
                                "_media-type": "application/vnd.ez.api.ContentInfo+json",
                                "_href": "/api/ezp/v2/content/objects/118"
                            }
                        },
                        "Fields": {
                            "field": [
                                {
                                    "id": 381,
                                    "fieldDefinitionIdentifier": "title",
                                    "languageCode": "eng-GB",
                                    "fieldTypeIdentifier": "ezstring",
                                    "fieldValue": "Test blog post"
                                },
                                {
                                    "id": 382,
                                    "fieldDefinitionIdentifier": "publication_date",
                                    "languageCode": "eng-GB",
                                    "fieldTypeIdentifier": "ezdatetime",
                                    "fieldValue": {
                                        "timestamp": 1513767600,
                                        "rfc850": "Wednesday, 20-Dec-17 11:00:00 GMT+0000"
                                    }
                                },
                                {
                                    "id": 383,
                                    "fieldDefinitionIdentifier": "author",
                                    "languageCode": "eng-GB",
                                    "fieldTypeIdentifier": "ezauthor",
                                    "fieldValue": [
                                        {
                                            "id": "1",
                                            "name": "Piotr",
                                            "email": "a@a.pl"
                                        }
                                    ]
                                },
                                {
                                    "id": 384,
                                    "fieldDefinitionIdentifier": "intro",
                                    "languageCode": "eng-GB",
                                    "fieldTypeIdentifier": "ezrichtext",
                                    "fieldValue": {
                                        "xml": "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<section xmlns=\"http://docbook.org/ns/docbook\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" xmlns:ezxhtml=\"http://ez.no/xmlns/ezpublish/docbook/xhtml\" xmlns:ezcustom=\"http://ez.no/xmlns/ezpublish/docbook/custom\" version=\"5.0-variant ezpublish-1.0\"><para>Test intro</para></section>\n",
                                        "xhtml5edit": "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<section xmlns=\"http://ez.no/namespaces/ezpublish5/xhtml5/edit\"><p>Test intro</p></section>\n"
                                    }
                                },
                                {
                                    "id": 385,
                                    "fieldDefinitionIdentifier": "body",
                                    "languageCode": "eng-GB",
                                    "fieldTypeIdentifier": "ezrichtext",
                                    "fieldValue": {
                                        "xml": "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<section xmlns=\"http://docbook.org/ns/docbook\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" xmlns:ezxhtml=\"http://ez.no/xmlns/ezpublish/docbook/xhtml\" xmlns:ezcustom=\"http://ez.no/xmlns/ezpublish/docbook/custom\" version=\"5.0-variant ezpublish-1.0\"><para>Test body</para></section>\n",
                                        "xhtml5edit": "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<section xmlns=\"http://ez.no/namespaces/ezpublish5/xhtml5/edit\"><p>Test body</p></section>\n"
                                    }
                                },
                                {
                                    "id": 386,
                                    "fieldDefinitionIdentifier": "image",
                                    "languageCode": "eng-GB",
                                    "fieldTypeIdentifier": "ezimage",
                                    "fieldValue": null
                                }
                            ]
                        },
                        "Relations": {
                            "_media-type": "application/vnd.ez.api.RelationList+json",
                            "_href": "/api/ezp/v2/content/objects/118/versions/1/relations",
                            "Relation": []
                        }
                    }
                },
                "Section": {
                    "_media-type": "application/vnd.ez.api.Section+json",
                    "_href": "/api/ezp/v2/content/sections/1"
                },
                "MainLocation": {
                    "_media-type": "application/vnd.ez.api.Location+json",
                    "_href": "/api/ezp/v2/content/locations/1/2/120"
                },
                "Locations": {
                    "_media-type": "application/vnd.ez.api.LocationList+json",
                    "_href": "/api/ezp/v2/content/objects/118/locations"
                },
                "Owner": {
                    "_media-type": "application/vnd.ez.api.User+json",
                    "_href": "/api/ezp/v2/user/users/14"
                },
                "lastModificationDate": "2017-12-11T13:34:48+01:00",
                "publishedDate": "2017-12-11T13:34:48+01:00",
                "mainLanguageCode": "eng-GB",
                "currentVersionNo": 1,
                "alwaysAvailable": false,
                "ObjectStates": {
                    "_media-type": "application/vnd.ez.api.ContentObjectStates+json",
                    "_href": "/api/ezp/v2/content/objects/118/objectstates"
                }
            }
        },
        {
            "location": {
                "_media-type": "application/vnd.ez.api.Location+json",
                "_href": "/api/ezp/v2/content/locations/1/2/121",
                "id": 121,
                "priority": 0,
                "hidden": false,
                "invisible": false,
                "ParentLocation": {
                    "_media-type": "application/vnd.ez.api.Location+json",
                    "_href": "/api/ezp/v2/content/locations/1/2"
                },
                "pathString": "/1/2/121/",
                "depth": 2,
                "childCount": 0,
                "remoteId": "5833ab84a22193daed65740245eb47cd",
                "Children": {
                    "_media-type": "application/vnd.ez.api.LocationList+json",
                    "_href": "/api/ezp/v2/content/locations/1/2/121/children"
                },
                "Content": {
                    "_media-type": "application/vnd.ez.api.Content+json",
                    "_href": "/api/ezp/v2/content/objects/119"
                },
                "sortField": "NAME",
                "sortOrder": "ASC",
                "UrlAliases": {
                    "_media-type": "application/vnd.ez.api.UrlAliasRefList+json",
                    "_href": "/api/ezp/v2/content/locations/1/2/121/urlaliases"
                },
                "ContentInfo": {
                    "_media-type": "application/vnd.ez.api.ContentInfo+json",
                    "_href": "/api/ezp/v2/content/objects/119",
                    "Content": {
                        "_media-type": "application/vnd.ez.api.ContentInfo+json",
                        "_href": "/api/ezp/v2/content/objects/119",
                        "_remoteId": "1804063765c0aa3e8db00ffcb461796e",
                        "_id": 119,
                        "ContentType": {
                            "_media-type": "application/vnd.ez.api.ContentType+json",
                            "_href": "/api/ezp/v2/content/types/21"
                        },
                        "Name": "Zrzut ekranu 2017-11-24 o 12.14.00.jpg visual-bug.jpg Mondadori",
                        "Versions": {
                            "_media-type": "application/vnd.ez.api.VersionList+json",
                            "_href": "/api/ezp/v2/content/objects/119/versions"
                        },
                        "CurrentVersion": {
                            "_media-type": "application/vnd.ez.api.Version+json",
                            "_href": "/api/ezp/v2/content/objects/119/currentversion"
                        },
                        "Section": {
                            "_media-type": "application/vnd.ez.api.Section+json",
                            "_href": "/api/ezp/v2/content/sections/1"
                        },
                        "Locations": {
                            "_media-type": "application/vnd.ez.api.LocationList+json",
                            "_href": "/api/ezp/v2/content/objects/119/locations"
                        },
                        "Owner": {
                            "_media-type": "application/vnd.ez.api.User+json",
                            "_href": "/api/ezp/v2/user/users/14"
                        },
                        "lastModificationDate": "2017-12-11T15:09:10+01:00",
                        "publishedDate": "2017-12-11T15:09:10+01:00",
                        "mainLanguageCode": "eng-GB",
                        "currentVersionNo": 1,
                        "alwaysAvailable": false,
                        "ObjectStates": {
                            "_media-type": "application/vnd.ez.api.ContentObjectStates+json",
                            "_href": "/api/ezp/v2/content/objects/119/objectstates"
                        }
                    }
                }
            },
            "content": {
                "_media-type": "application/vnd.ez.api.Content+json",
                "_href": "/api/ezp/v2/content/objects/119",
                "_remoteId": "1804063765c0aa3e8db00ffcb461796e",
                "_id": 119,
                "ContentType": {
                    "_media-type": "application/vnd.ez.api.ContentType+json",
                    "_href": "/api/ezp/v2/content/types/21"
                },
                "Name": "Zrzut ekranu 2017-11-24 o 12.14.00.jpg visual-bug.jpg Mondadori",
                "Versions": {
                    "_media-type": "application/vnd.ez.api.VersionList+json",
                    "_href": "/api/ezp/v2/content/objects/119/versions"
                },
                "CurrentVersion": {
                    "_media-type": "application/vnd.ez.api.Version+json",
                    "_href": "/api/ezp/v2/content/objects/119/currentversion",
                    "Version": {
                        "_media-type": "application/vnd.ez.api.Version+json",
                        "_href": "/api/ezp/v2/content/objects/119/versions/1",
                        "VersionInfo": {
                            "id": 621,
                            "versionNo": 1,
                            "status": "PUBLISHED",
                            "modificationDate": "2017-12-11T15:09:10+01:00",
                            "Creator": {
                                "_media-type": "application/vnd.ez.api.User+json",
                                "_href": "/api/ezp/v2/user/users/14"
                            },
                            "creationDate": "2017-12-11T15:09:10+01:00",
                            "initialLanguageCode": "eng-GB",
                            "languageCodes": "eng-GB",
                            "VersionTranslationInfo": {
                                "_media-type": "application/vnd.ez.api.VersionTranslationInfo+json",
                                "Language": [
                                    {
                                        "languageCode": "eng-GB"
                                    }
                                ]
                            },
                            "names": {
                                "value": [
                                    {
                                        "_languageCode": "eng-GB",
                                        "#text": "Zrzut ekranu 2017-11-24 o 12.14.00.jpg visual-bug.jpg Mondadori"
                                    }
                                ]
                            },
                            "Content": {
                                "_media-type": "application/vnd.ez.api.ContentInfo+json",
                                "_href": "/api/ezp/v2/content/objects/119"
                            }
                        },
                        "Fields": {
                            "field": [
                                {
                                    "id": 387,
                                    "fieldDefinitionIdentifier": "new_ezobjectrelationlist_1",
                                    "languageCode": "eng-GB",
                                    "fieldTypeIdentifier": "ezobjectrelationlist",
                                    "fieldValue": {
                                        "destinationContentIds": [
                                            117,
                                            115,
                                            89
                                        ],
                                        "destinationContentHrefs": [
                                            "/api/ezp/v2/content/objects/117",
                                            "/api/ezp/v2/content/objects/115",
                                            "/api/ezp/v2/content/objects/89"
                                        ]
                                    }
                                },
                                {
                                    "id": 388,
                                    "fieldDefinitionIdentifier": "new_ezobjectrelation_2",
                                    "languageCode": "eng-GB",
                                    "fieldTypeIdentifier": "ezobjectrelation",
                                    "fieldValue": {
                                        "destinationContentId": null
                                    }
                                }
                            ]
                        },
                        "Relations": {
                            "_media-type": "application/vnd.ez.api.RelationList+json",
                            "_href": "/api/ezp/v2/content/objects/119/versions/1/relations",
                            "Relation": [
                                {
                                    "_media-type": "application/vnd.ez.api.Relation+json",
                                    "_href": "/api/ezp/v2/content/objects/119/versions/1/relations/3",
                                    "SourceContent": {
                                        "_media-type": "application/vnd.ez.api.ContentInfo+json",
                                        "_href": "/api/ezp/v2/content/objects/119"
                                    },
                                    "DestinationContent": {
                                        "_media-type": "application/vnd.ez.api.ContentInfo+json",
                                        "_href": "/api/ezp/v2/content/objects/117"
                                    },
                                    "SourceFieldDefinitionIdentifier": "new_ezobjectrelationlist_1",
                                    "RelationType": "ATTRIBUTE"
                                },
                                {
                                    "_media-type": "application/vnd.ez.api.Relation+json",
                                    "_href": "/api/ezp/v2/content/objects/119/versions/1/relations/4",
                                    "SourceContent": {
                                        "_media-type": "application/vnd.ez.api.ContentInfo+json",
                                        "_href": "/api/ezp/v2/content/objects/119"
                                    },
                                    "DestinationContent": {
                                        "_media-type": "application/vnd.ez.api.ContentInfo+json",
                                        "_href": "/api/ezp/v2/content/objects/115"
                                    },
                                    "SourceFieldDefinitionIdentifier": "new_ezobjectrelationlist_1",
                                    "RelationType": "ATTRIBUTE"
                                },
                                {
                                    "_media-type": "application/vnd.ez.api.Relation+json",
                                    "_href": "/api/ezp/v2/content/objects/119/versions/1/relations/5",
                                    "SourceContent": {
                                        "_media-type": "application/vnd.ez.api.ContentInfo+json",
                                        "_href": "/api/ezp/v2/content/objects/119"
                                    },
                                    "DestinationContent": {
                                        "_media-type": "application/vnd.ez.api.ContentInfo+json",
                                        "_href": "/api/ezp/v2/content/objects/89"
                                    },
                                    "SourceFieldDefinitionIdentifier": "new_ezobjectrelationlist_1",
                                    "RelationType": "ATTRIBUTE"
                                }
                            ]
                        }
                    }
                },
                "Section": {
                    "_media-type": "application/vnd.ez.api.Section+json",
                    "_href": "/api/ezp/v2/content/sections/1"
                },
                "MainLocation": {
                    "_media-type": "application/vnd.ez.api.Location+json",
                    "_href": "/api/ezp/v2/content/locations/1/2/121"
                },
                "Locations": {
                    "_media-type": "application/vnd.ez.api.LocationList+json",
                    "_href": "/api/ezp/v2/content/objects/119/locations"
                },
                "Owner": {
                    "_media-type": "application/vnd.ez.api.User+json",
                    "_href": "/api/ezp/v2/user/users/14"
                },
                "lastModificationDate": "2017-12-11T15:09:10+01:00",
                "publishedDate": "2017-12-11T15:09:10+01:00",
                "mainLanguageCode": "eng-GB",
                "currentVersionNo": 1,
                "alwaysAvailable": false,
                "ObjectStates": {
                    "_media-type": "application/vnd.ez.api.ContentObjectStates+json",
                    "_href": "/api/ezp/v2/content/objects/119/objectstates"
                }
            }
        },
        {
            "location": {
                "_media-type": "application/vnd.ez.api.Location+json",
                "_href": "/api/ezp/v2/content/locations/1/2/122",
                "id": 122,
                "priority": 0,
                "hidden": false,
                "invisible": false,
                "ParentLocation": {
                    "_media-type": "application/vnd.ez.api.Location+json",
                    "_href": "/api/ezp/v2/content/locations/1/2"
                },
                "pathString": "/1/2/122/",
                "depth": 2,
                "childCount": 0,
                "remoteId": "c5ca8f8c0efd049d75144fcf34c103dd",
                "Children": {
                    "_media-type": "application/vnd.ez.api.LocationList+json",
                    "_href": "/api/ezp/v2/content/locations/1/2/122/children"
                },
                "Content": {
                    "_media-type": "application/vnd.ez.api.Content+json",
                    "_href": "/api/ezp/v2/content/objects/120"
                },
                "sortField": "NAME",
                "sortOrder": "ASC",
                "UrlAliases": {
                    "_media-type": "application/vnd.ez.api.UrlAliasRefList+json",
                    "_href": "/api/ezp/v2/content/locations/1/2/122/urlaliases"
                },
                "ContentInfo": {
                    "_media-type": "application/vnd.ez.api.ContentInfo+json",
                    "_href": "/api/ezp/v2/content/objects/120",
                    "Content": {
                        "_media-type": "application/vnd.ez.api.ContentInfo+json",
                        "_href": "/api/ezp/v2/content/objects/120",
                        "_remoteId": "5c14b537c1e182e356b47649578a1384",
                        "_id": 120,
                        "ContentType": {
                            "_media-type": "application/vnd.ez.api.ContentType+json",
                            "_href": "/api/ezp/v2/content/types/21"
                        },
                        "Name": "Zrzut ekranu 2017-11-24 o 12.14.00.jpg blog-stats-30-days-23.09.2017-23.10.2017.jpg visual-bug.jpg Zrzut ekranu 2017-11-22 o 09.47.35.jpg Zrzut ekr...",
                        "Versions": {
                            "_media-type": "application/vnd.ez.api.VersionList+json",
                            "_href": "/api/ezp/v2/content/objects/120/versions"
                        },
                        "CurrentVersion": {
                            "_media-type": "application/vnd.ez.api.Version+json",
                            "_href": "/api/ezp/v2/content/objects/120/currentversion"
                        },
                        "Section": {
                            "_media-type": "application/vnd.ez.api.Section+json",
                            "_href": "/api/ezp/v2/content/sections/1"
                        },
                        "Locations": {
                            "_media-type": "application/vnd.ez.api.LocationList+json",
                            "_href": "/api/ezp/v2/content/objects/120/locations"
                        },
                        "Owner": {
                            "_media-type": "application/vnd.ez.api.User+json",
                            "_href": "/api/ezp/v2/user/users/14"
                        },
                        "lastModificationDate": "2017-12-11T15:24:30+01:00",
                        "publishedDate": "2017-12-11T15:24:30+01:00",
                        "mainLanguageCode": "eng-GB",
                        "currentVersionNo": 1,
                        "alwaysAvailable": false,
                        "ObjectStates": {
                            "_media-type": "application/vnd.ez.api.ContentObjectStates+json",
                            "_href": "/api/ezp/v2/content/objects/120/objectstates"
                        }
                    }
                }
            },
            "content": {
                "_media-type": "application/vnd.ez.api.Content+json",
                "_href": "/api/ezp/v2/content/objects/120",
                "_remoteId": "5c14b537c1e182e356b47649578a1384",
                "_id": 120,
                "ContentType": {
                    "_media-type": "application/vnd.ez.api.ContentType+json",
                    "_href": "/api/ezp/v2/content/types/21"
                },
                "Name": "Zrzut ekranu 2017-11-24 o 12.14.00.jpg blog-stats-30-days-23.09.2017-23.10.2017.jpg visual-bug.jpg Zrzut ekranu 2017-11-22 o 09.47.35.jpg Zrzut ekr...",
                "Versions": {
                    "_media-type": "application/vnd.ez.api.VersionList+json",
                    "_href": "/api/ezp/v2/content/objects/120/versions"
                },
                "CurrentVersion": {
                    "_media-type": "application/vnd.ez.api.Version+json",
                    "_href": "/api/ezp/v2/content/objects/120/currentversion",
                    "Version": {
                        "_media-type": "application/vnd.ez.api.Version+json",
                        "_href": "/api/ezp/v2/content/objects/120/versions/1",
                        "VersionInfo": {
                            "id": 623,
                            "versionNo": 1,
                            "status": "PUBLISHED",
                            "modificationDate": "2017-12-11T15:24:30+01:00",
                            "Creator": {
                                "_media-type": "application/vnd.ez.api.User+json",
                                "_href": "/api/ezp/v2/user/users/14"
                            },
                            "creationDate": "2017-12-11T15:24:30+01:00",
                            "initialLanguageCode": "eng-GB",
                            "languageCodes": "eng-GB",
                            "VersionTranslationInfo": {
                                "_media-type": "application/vnd.ez.api.VersionTranslationInfo+json",
                                "Language": [
                                    {
                                        "languageCode": "eng-GB"
                                    }
                                ]
                            },
                            "names": {
                                "value": [
                                    {
                                        "_languageCode": "eng-GB",
                                        "#text": "Zrzut ekranu 2017-11-24 o 12.14.00.jpg blog-stats-30-days-23.09.2017-23.10.2017.jpg visual-bug.jpg Zrzut ekranu 2017-11-22 o 09.47.35.jpg Zrzut ekr..."
                                    }
                                ]
                            },
                            "Content": {
                                "_media-type": "application/vnd.ez.api.ContentInfo+json",
                                "_href": "/api/ezp/v2/content/objects/120"
                            }
                        },
                        "Fields": {
                            "field": [
                                {
                                    "id": 389,
                                    "fieldDefinitionIdentifier": "new_ezobjectrelationlist_1",
                                    "languageCode": "eng-GB",
                                    "fieldTypeIdentifier": "ezobjectrelationlist",
                                    "fieldValue": {
                                        "destinationContentIds": [
                                            117,
                                            116,
                                            115,
                                            113,
                                            111
                                        ],
                                        "destinationContentHrefs": [
                                            "/api/ezp/v2/content/objects/117",
                                            "/api/ezp/v2/content/objects/116",
                                            "/api/ezp/v2/content/objects/115",
                                            "/api/ezp/v2/content/objects/113",
                                            "/api/ezp/v2/content/objects/111"
                                        ]
                                    }
                                },
                                {
                                    "id": 390,
                                    "fieldDefinitionIdentifier": "new_ezobjectrelation_2",
                                    "languageCode": "eng-GB",
                                    "fieldTypeIdentifier": "ezobjectrelation",
                                    "fieldValue": {
                                        "destinationContentId": null
                                    }
                                }
                            ]
                        },
                        "Relations": {
                            "_media-type": "application/vnd.ez.api.RelationList+json",
                            "_href": "/api/ezp/v2/content/objects/120/versions/1/relations",
                            "Relation": [
                                {
                                    "_media-type": "application/vnd.ez.api.Relation+json",
                                    "_href": "/api/ezp/v2/content/objects/120/versions/1/relations/9",
                                    "SourceContent": {
                                        "_media-type": "application/vnd.ez.api.ContentInfo+json",
                                        "_href": "/api/ezp/v2/content/objects/120"
                                    },
                                    "DestinationContent": {
                                        "_media-type": "application/vnd.ez.api.ContentInfo+json",
                                        "_href": "/api/ezp/v2/content/objects/117"
                                    },
                                    "SourceFieldDefinitionIdentifier": "new_ezobjectrelationlist_1",
                                    "RelationType": "ATTRIBUTE"
                                },
                                {
                                    "_media-type": "application/vnd.ez.api.Relation+json",
                                    "_href": "/api/ezp/v2/content/objects/120/versions/1/relations/10",
                                    "SourceContent": {
                                        "_media-type": "application/vnd.ez.api.ContentInfo+json",
                                        "_href": "/api/ezp/v2/content/objects/120"
                                    },
                                    "DestinationContent": {
                                        "_media-type": "application/vnd.ez.api.ContentInfo+json",
                                        "_href": "/api/ezp/v2/content/objects/116"
                                    },
                                    "SourceFieldDefinitionIdentifier": "new_ezobjectrelationlist_1",
                                    "RelationType": "ATTRIBUTE"
                                },
                                {
                                    "_media-type": "application/vnd.ez.api.Relation+json",
                                    "_href": "/api/ezp/v2/content/objects/120/versions/1/relations/11",
                                    "SourceContent": {
                                        "_media-type": "application/vnd.ez.api.ContentInfo+json",
                                        "_href": "/api/ezp/v2/content/objects/120"
                                    },
                                    "DestinationContent": {
                                        "_media-type": "application/vnd.ez.api.ContentInfo+json",
                                        "_href": "/api/ezp/v2/content/objects/115"
                                    },
                                    "SourceFieldDefinitionIdentifier": "new_ezobjectrelationlist_1",
                                    "RelationType": "ATTRIBUTE"
                                },
                                {
                                    "_media-type": "application/vnd.ez.api.Relation+json",
                                    "_href": "/api/ezp/v2/content/objects/120/versions/1/relations/12",
                                    "SourceContent": {
                                        "_media-type": "application/vnd.ez.api.ContentInfo+json",
                                        "_href": "/api/ezp/v2/content/objects/120"
                                    },
                                    "DestinationContent": {
                                        "_media-type": "application/vnd.ez.api.ContentInfo+json",
                                        "_href": "/api/ezp/v2/content/objects/113"
                                    },
                                    "SourceFieldDefinitionIdentifier": "new_ezobjectrelationlist_1",
                                    "RelationType": "ATTRIBUTE"
                                },
                                {
                                    "_media-type": "application/vnd.ez.api.Relation+json",
                                    "_href": "/api/ezp/v2/content/objects/120/versions/1/relations/13",
                                    "SourceContent": {
                                        "_media-type": "application/vnd.ez.api.ContentInfo+json",
                                        "_href": "/api/ezp/v2/content/objects/120"
                                    },
                                    "DestinationContent": {
                                        "_media-type": "application/vnd.ez.api.ContentInfo+json",
                                        "_href": "/api/ezp/v2/content/objects/111"
                                    },
                                    "SourceFieldDefinitionIdentifier": "new_ezobjectrelationlist_1",
                                    "RelationType": "ATTRIBUTE"
                                }
                            ]
                        }
                    }
                },
                "Section": {
                    "_media-type": "application/vnd.ez.api.Section+json",
                    "_href": "/api/ezp/v2/content/sections/1"
                },
                "MainLocation": {
                    "_media-type": "application/vnd.ez.api.Location+json",
                    "_href": "/api/ezp/v2/content/locations/1/2/122"
                },
                "Locations": {
                    "_media-type": "application/vnd.ez.api.LocationList+json",
                    "_href": "/api/ezp/v2/content/objects/120/locations"
                },
                "Owner": {
                    "_media-type": "application/vnd.ez.api.User+json",
                    "_href": "/api/ezp/v2/user/users/14"
                },
                "lastModificationDate": "2017-12-11T15:24:30+01:00",
                "publishedDate": "2017-12-11T15:24:30+01:00",
                "mainLanguageCode": "eng-GB",
                "currentVersionNo": 1,
                "alwaysAvailable": false,
                "ObjectStates": {
                    "_media-type": "application/vnd.ez.api.ContentObjectStates+json",
                    "_href": "/api/ezp/v2/content/objects/120/objectstates"
                }
            }
        },
        {
            "location": {
                "_media-type": "application/vnd.ez.api.Location+json",
                "_href": "/api/ezp/v2/content/locations/1/2/123",
                "id": 123,
                "priority": 0,
                "hidden": false,
                "invisible": false,
                "ParentLocation": {
                    "_media-type": "application/vnd.ez.api.Location+json",
                    "_href": "/api/ezp/v2/content/locations/1/2"
                },
                "pathString": "/1/2/123/",
                "depth": 2,
                "childCount": 0,
                "remoteId": "88db3ca5db8fdc24e8332e5aa974f0d9",
                "Children": {
                    "_media-type": "application/vnd.ez.api.LocationList+json",
                    "_href": "/api/ezp/v2/content/locations/1/2/123/children"
                },
                "Content": {
                    "_media-type": "application/vnd.ez.api.Content+json",
                    "_href": "/api/ezp/v2/content/objects/121"
                },
                "sortField": "NAME",
                "sortOrder": "ASC",
                "UrlAliases": {
                    "_media-type": "application/vnd.ez.api.UrlAliasRefList+json",
                    "_href": "/api/ezp/v2/content/locations/1/2/123/urlaliases"
                },
                "ContentInfo": {
                    "_media-type": "application/vnd.ez.api.ContentInfo+json",
                    "_href": "/api/ezp/v2/content/objects/121",
                    "Content": {
                        "_media-type": "application/vnd.ez.api.ContentInfo+json",
                        "_href": "/api/ezp/v2/content/objects/121",
                        "_remoteId": "b4d17487078af990f53cf1574e6ce433",
                        "_id": 121,
                        "ContentType": {
                            "_media-type": "application/vnd.ez.api.ContentType+json",
                            "_href": "/api/ezp/v2/content/types/25"
                        },
                        "Name": "",
                        "Versions": {
                            "_media-type": "application/vnd.ez.api.VersionList+json",
                            "_href": "/api/ezp/v2/content/objects/121/versions"
                        },
                        "CurrentVersion": {
                            "_media-type": "application/vnd.ez.api.Version+json",
                            "_href": "/api/ezp/v2/content/objects/121/currentversion"
                        },
                        "Section": {
                            "_media-type": "application/vnd.ez.api.Section+json",
                            "_href": "/api/ezp/v2/content/sections/1"
                        },
                        "Locations": {
                            "_media-type": "application/vnd.ez.api.LocationList+json",
                            "_href": "/api/ezp/v2/content/objects/121/locations"
                        },
                        "Owner": {
                            "_media-type": "application/vnd.ez.api.User+json",
                            "_href": "/api/ezp/v2/user/users/14"
                        },
                        "lastModificationDate": "2017-12-11T15:46:00+01:00",
                        "publishedDate": "2017-12-11T15:46:00+01:00",
                        "mainLanguageCode": "eng-GB",
                        "currentVersionNo": 1,
                        "alwaysAvailable": false,
                        "ObjectStates": {
                            "_media-type": "application/vnd.ez.api.ContentObjectStates+json",
                            "_href": "/api/ezp/v2/content/objects/121/objectstates"
                        }
                    }
                }
            },
            "content": {
                "_media-type": "application/vnd.ez.api.Content+json",
                "_href": "/api/ezp/v2/content/objects/121",
                "_remoteId": "b4d17487078af990f53cf1574e6ce433",
                "_id": 121,
                "ContentType": {
                    "_media-type": "application/vnd.ez.api.ContentType+json",
                    "_href": "/api/ezp/v2/content/types/25"
                },
                "Name": "",
                "Versions": {
                    "_media-type": "application/vnd.ez.api.VersionList+json",
                    "_href": "/api/ezp/v2/content/objects/121/versions"
                },
                "CurrentVersion": {
                    "_media-type": "application/vnd.ez.api.Version+json",
                    "_href": "/api/ezp/v2/content/objects/121/currentversion",
                    "Version": {
                        "_media-type": "application/vnd.ez.api.Version+json",
                        "_href": "/api/ezp/v2/content/objects/121/versions/1",
                        "VersionInfo": {
                            "id": 625,
                            "versionNo": 1,
                            "status": "PUBLISHED",
                            "modificationDate": "2017-12-11T15:46:00+01:00",
                            "Creator": {
                                "_media-type": "application/vnd.ez.api.User+json",
                                "_href": "/api/ezp/v2/user/users/14"
                            },
                            "creationDate": "2017-12-11T15:46:00+01:00",
                            "initialLanguageCode": "eng-GB",
                            "languageCodes": "eng-GB",
                            "VersionTranslationInfo": {
                                "_media-type": "application/vnd.ez.api.VersionTranslationInfo+json",
                                "Language": [
                                    {
                                        "languageCode": "eng-GB"
                                    }
                                ]
                            },
                            "names": {
                                "value": [
                                    {
                                        "_languageCode": "eng-GB",
                                        "#text": ""
                                    }
                                ]
                            },
                            "Content": {
                                "_media-type": "application/vnd.ez.api.ContentInfo+json",
                                "_href": "/api/ezp/v2/content/objects/121"
                            }
                        },
                        "Fields": {
                            "field": [
                                {
                                    "id": 391,
                                    "fieldDefinitionIdentifier": "new_ezobjectrelationlist_1",
                                    "languageCode": "eng-GB",
                                    "fieldTypeIdentifier": "ezobjectrelationlist",
                                    "fieldValue": {
                                        "destinationContentIds": [],
                                        "destinationContentHrefs": []
                                    }
                                },
                                {
                                    "id": 392,
                                    "fieldDefinitionIdentifier": "title",
                                    "languageCode": "eng-GB",
                                    "fieldTypeIdentifier": "ezstring",
                                    "fieldValue": "Multiple relations content"
                                }
                            ]
                        },
                        "Relations": {
                            "_media-type": "application/vnd.ez.api.RelationList+json",
                            "_href": "/api/ezp/v2/content/objects/121/versions/1/relations",
                            "Relation": []
                        }
                    }
                },
                "Section": {
                    "_media-type": "application/vnd.ez.api.Section+json",
                    "_href": "/api/ezp/v2/content/sections/1"
                },
                "MainLocation": {
                    "_media-type": "application/vnd.ez.api.Location+json",
                    "_href": "/api/ezp/v2/content/locations/1/2/123"
                },
                "Locations": {
                    "_media-type": "application/vnd.ez.api.LocationList+json",
                    "_href": "/api/ezp/v2/content/objects/121/locations"
                },
                "Owner": {
                    "_media-type": "application/vnd.ez.api.User+json",
                    "_href": "/api/ezp/v2/user/users/14"
                },
                "lastModificationDate": "2017-12-11T15:46:00+01:00",
                "publishedDate": "2017-12-11T15:46:00+01:00",
                "mainLanguageCode": "eng-GB",
                "currentVersionNo": 1,
                "alwaysAvailable": false,
                "ObjectStates": {
                    "_media-type": "application/vnd.ez.api.ContentObjectStates+json",
                    "_href": "/api/ezp/v2/content/objects/121/objectstates"
                }
            }
        },
        {
            "location": {
                "_media-type": "application/vnd.ez.api.Location+json",
                "_href": "/api/ezp/v2/content/locations/1/2/124",
                "id": 124,
                "priority": 0,
                "hidden": false,
                "invisible": false,
                "ParentLocation": {
                    "_media-type": "application/vnd.ez.api.Location+json",
                    "_href": "/api/ezp/v2/content/locations/1/2"
                },
                "pathString": "/1/2/124/",
                "depth": 2,
                "childCount": 0,
                "remoteId": "0c1bf48aa645bac2280328ca3bacdfde",
                "Children": {
                    "_media-type": "application/vnd.ez.api.LocationList+json",
                    "_href": "/api/ezp/v2/content/locations/1/2/124/children"
                },
                "Content": {
                    "_media-type": "application/vnd.ez.api.Content+json",
                    "_href": "/api/ezp/v2/content/objects/122"
                },
                "sortField": "NAME",
                "sortOrder": "ASC",
                "UrlAliases": {
                    "_media-type": "application/vnd.ez.api.UrlAliasRefList+json",
                    "_href": "/api/ezp/v2/content/locations/1/2/124/urlaliases"
                },
                "ContentInfo": {
                    "_media-type": "application/vnd.ez.api.ContentInfo+json",
                    "_href": "/api/ezp/v2/content/objects/122",
                    "Content": {
                        "_media-type": "application/vnd.ez.api.ContentInfo+json",
                        "_href": "/api/ezp/v2/content/objects/122",
                        "_remoteId": "f8e833ca5788cbb6c52d2b33035d12ea",
                        "_id": 122,
                        "ContentType": {
                            "_media-type": "application/vnd.ez.api.ContentType+json",
                            "_href": "/api/ezp/v2/content/types/21"
                        },
                        "Name": "Zrzut ekranu 2017-11-24 o 12.14.00.jpg blog-stats-30-days-23.09.2017-23.10.2017.jpg",
                        "Versions": {
                            "_media-type": "application/vnd.ez.api.VersionList+json",
                            "_href": "/api/ezp/v2/content/objects/122/versions"
                        },
                        "CurrentVersion": {
                            "_media-type": "application/vnd.ez.api.Version+json",
                            "_href": "/api/ezp/v2/content/objects/122/currentversion"
                        },
                        "Section": {
                            "_media-type": "application/vnd.ez.api.Section+json",
                            "_href": "/api/ezp/v2/content/sections/1"
                        },
                        "Locations": {
                            "_media-type": "application/vnd.ez.api.LocationList+json",
                            "_href": "/api/ezp/v2/content/objects/122/locations"
                        },
                        "Owner": {
                            "_media-type": "application/vnd.ez.api.User+json",
                            "_href": "/api/ezp/v2/user/users/14"
                        },
                        "lastModificationDate": "2017-12-12T09:57:44+01:00",
                        "publishedDate": "2017-12-12T09:57:44+01:00",
                        "mainLanguageCode": "eng-GB",
                        "currentVersionNo": 1,
                        "alwaysAvailable": false,
                        "ObjectStates": {
                            "_media-type": "application/vnd.ez.api.ContentObjectStates+json",
                            "_href": "/api/ezp/v2/content/objects/122/objectstates"
                        }
                    }
                }
            },
            "content": {
                "_media-type": "application/vnd.ez.api.Content+json",
                "_href": "/api/ezp/v2/content/objects/122",
                "_remoteId": "f8e833ca5788cbb6c52d2b33035d12ea",
                "_id": 122,
                "ContentType": {
                    "_media-type": "application/vnd.ez.api.ContentType+json",
                    "_href": "/api/ezp/v2/content/types/21"
                },
                "Name": "Zrzut ekranu 2017-11-24 o 12.14.00.jpg blog-stats-30-days-23.09.2017-23.10.2017.jpg",
                "Versions": {
                    "_media-type": "application/vnd.ez.api.VersionList+json",
                    "_href": "/api/ezp/v2/content/objects/122/versions"
                },
                "CurrentVersion": {
                    "_media-type": "application/vnd.ez.api.Version+json",
                    "_href": "/api/ezp/v2/content/objects/122/currentversion",
                    "Version": {
                        "_media-type": "application/vnd.ez.api.Version+json",
                        "_href": "/api/ezp/v2/content/objects/122/versions/1",
                        "VersionInfo": {
                            "id": 627,
                            "versionNo": 1,
                            "status": "PUBLISHED",
                            "modificationDate": "2017-12-12T09:57:44+01:00",
                            "Creator": {
                                "_media-type": "application/vnd.ez.api.User+json",
                                "_href": "/api/ezp/v2/user/users/14"
                            },
                            "creationDate": "2017-12-12T09:57:44+01:00",
                            "initialLanguageCode": "eng-GB",
                            "languageCodes": "eng-GB",
                            "VersionTranslationInfo": {
                                "_media-type": "application/vnd.ez.api.VersionTranslationInfo+json",
                                "Language": [
                                    {
                                        "languageCode": "eng-GB"
                                    }
                                ]
                            },
                            "names": {
                                "value": [
                                    {
                                        "_languageCode": "eng-GB",
                                        "#text": "Zrzut ekranu 2017-11-24 o 12.14.00.jpg blog-stats-30-days-23.09.2017-23.10.2017.jpg"
                                    }
                                ]
                            },
                            "Content": {
                                "_media-type": "application/vnd.ez.api.ContentInfo+json",
                                "_href": "/api/ezp/v2/content/objects/122"
                            }
                        },
                        "Fields": {
                            "field": [
                                {
                                    "id": 393,
                                    "fieldDefinitionIdentifier": "new_ezobjectrelationlist_1",
                                    "languageCode": "eng-GB",
                                    "fieldTypeIdentifier": "ezobjectrelationlist",
                                    "fieldValue": {
                                        "destinationContentIds": [
                                            117,
                                            116
                                        ],
                                        "destinationContentHrefs": [
                                            "/api/ezp/v2/content/objects/117",
                                            "/api/ezp/v2/content/objects/116"
                                        ]
                                    }
                                },
                                {
                                    "id": 394,
                                    "fieldDefinitionIdentifier": "new_ezobjectrelation_2",
                                    "languageCode": "eng-GB",
                                    "fieldTypeIdentifier": "ezobjectrelation",
                                    "fieldValue": {
                                        "destinationContentId": 116,
                                        "destinationContentHref": "/api/ezp/v2/content/objects/116"
                                    }
                                }
                            ]
                        },
                        "Relations": {
                            "_media-type": "application/vnd.ez.api.RelationList+json",
                            "_href": "/api/ezp/v2/content/objects/122/versions/1/relations",
                            "Relation": [
                                {
                                    "_media-type": "application/vnd.ez.api.Relation+json",
                                    "_href": "/api/ezp/v2/content/objects/122/versions/1/relations/19",
                                    "SourceContent": {
                                        "_media-type": "application/vnd.ez.api.ContentInfo+json",
                                        "_href": "/api/ezp/v2/content/objects/122"
                                    },
                                    "DestinationContent": {
                                        "_media-type": "application/vnd.ez.api.ContentInfo+json",
                                        "_href": "/api/ezp/v2/content/objects/117"
                                    },
                                    "SourceFieldDefinitionIdentifier": "new_ezobjectrelationlist_1",
                                    "RelationType": "ATTRIBUTE"
                                },
                                {
                                    "_media-type": "application/vnd.ez.api.Relation+json",
                                    "_href": "/api/ezp/v2/content/objects/122/versions/1/relations/20",
                                    "SourceContent": {
                                        "_media-type": "application/vnd.ez.api.ContentInfo+json",
                                        "_href": "/api/ezp/v2/content/objects/122"
                                    },
                                    "DestinationContent": {
                                        "_media-type": "application/vnd.ez.api.ContentInfo+json",
                                        "_href": "/api/ezp/v2/content/objects/116"
                                    },
                                    "SourceFieldDefinitionIdentifier": "new_ezobjectrelationlist_1",
                                    "RelationType": "ATTRIBUTE"
                                },
                                {
                                    "_media-type": "application/vnd.ez.api.Relation+json",
                                    "_href": "/api/ezp/v2/content/objects/122/versions/1/relations/21",
                                    "SourceContent": {
                                        "_media-type": "application/vnd.ez.api.ContentInfo+json",
                                        "_href": "/api/ezp/v2/content/objects/122"
                                    },
                                    "DestinationContent": {
                                        "_media-type": "application/vnd.ez.api.ContentInfo+json",
                                        "_href": "/api/ezp/v2/content/objects/116"
                                    },
                                    "SourceFieldDefinitionIdentifier": "new_ezobjectrelation_2",
                                    "RelationType": "ATTRIBUTE"
                                }
                            ]
                        }
                    }
                },
                "Section": {
                    "_media-type": "application/vnd.ez.api.Section+json",
                    "_href": "/api/ezp/v2/content/sections/1"
                },
                "MainLocation": {
                    "_media-type": "application/vnd.ez.api.Location+json",
                    "_href": "/api/ezp/v2/content/locations/1/2/124"
                },
                "Locations": {
                    "_media-type": "application/vnd.ez.api.LocationList+json",
                    "_href": "/api/ezp/v2/content/objects/122/locations"
                },
                "Owner": {
                    "_media-type": "application/vnd.ez.api.User+json",
                    "_href": "/api/ezp/v2/user/users/14"
                },
                "lastModificationDate": "2017-12-12T09:57:44+01:00",
                "publishedDate": "2017-12-12T09:57:44+01:00",
                "mainLanguageCode": "eng-GB",
                "currentVersionNo": 1,
                "alwaysAvailable": false,
                "ObjectStates": {
                    "_media-type": "application/vnd.ez.api.ContentObjectStates+json",
                    "_href": "/api/ezp/v2/content/objects/122/objectstates"
                }
            }
        },
        {
            "location": {
                "_media-type": "application/vnd.ez.api.Location+json",
                "_href": "/api/ezp/v2/content/locations/1/2/62",
                "id": 62,
                "priority": 10,
                "hidden": false,
                "invisible": false,
                "ParentLocation": {
                    "_media-type": "application/vnd.ez.api.Location+json",
                    "_href": "/api/ezp/v2/content/locations/1/2"
                },
                "pathString": "/1/2/62/",
                "depth": 2,
                "childCount": 3,
                "remoteId": "f751fb1fd45e41589f73cd4604d4dddc",
                "Children": {
                    "_media-type": "application/vnd.ez.api.LocationList+json",
                    "_href": "/api/ezp/v2/content/locations/1/2/62/children"
                },
                "Content": {
                    "_media-type": "application/vnd.ez.api.Content+json",
                    "_href": "/api/ezp/v2/content/objects/60"
                },
                "sortField": "PATH",
                "sortOrder": "ASC",
                "UrlAliases": {
                    "_media-type": "application/vnd.ez.api.UrlAliasRefList+json",
                    "_href": "/api/ezp/v2/content/locations/1/2/62/urlaliases"
                },
                "ContentInfo": {
                    "_media-type": "application/vnd.ez.api.ContentInfo+json",
                    "_href": "/api/ezp/v2/content/objects/60",
                    "Content": {
                        "_media-type": "application/vnd.ez.api.ContentInfo+json",
                        "_href": "/api/ezp/v2/content/objects/60",
                        "_remoteId": "0385f9b74351ddbc4d209410508525cf",
                        "_id": 60,
                        "ContentType": {
                            "_media-type": "application/vnd.ez.api.ContentType+json",
                            "_href": "/api/ezp/v2/content/types/17"
                        },
                        "Name": "Top Stories",
                        "Versions": {
                            "_media-type": "application/vnd.ez.api.VersionList+json",
                            "_href": "/api/ezp/v2/content/objects/60/versions"
                        },
                        "CurrentVersion": {
                            "_media-type": "application/vnd.ez.api.Version+json",
                            "_href": "/api/ezp/v2/content/objects/60/currentversion"
                        },
                        "Section": {
                            "_media-type": "application/vnd.ez.api.Section+json",
                            "_href": "/api/ezp/v2/content/sections/1"
                        },
                        "Locations": {
                            "_media-type": "application/vnd.ez.api.LocationList+json",
                            "_href": "/api/ezp/v2/content/objects/60/locations"
                        },
                        "Owner": {
                            "_media-type": "application/vnd.ez.api.User+json",
                            "_href": "/api/ezp/v2/user/users/14"
                        },
                        "lastModificationDate": "2015-11-28T00:36:08+01:00",
                        "publishedDate": "2015-11-28T00:36:08+01:00",
                        "mainLanguageCode": "eng-GB",
                        "currentVersionNo": 6,
                        "alwaysAvailable": true,
                        "ObjectStates": {
                            "_media-type": "application/vnd.ez.api.ContentObjectStates+json",
                            "_href": "/api/ezp/v2/content/objects/60/objectstates"
                        }
                    }
                }
            },
            "content": {
                "_media-type": "application/vnd.ez.api.Content+json",
                "_href": "/api/ezp/v2/content/objects/60",
                "_remoteId": "0385f9b74351ddbc4d209410508525cf",
                "_id": 60,
                "ContentType": {
                    "_media-type": "application/vnd.ez.api.ContentType+json",
                    "_href": "/api/ezp/v2/content/types/17"
                },
                "Name": "Top Stories",
                "Versions": {
                    "_media-type": "application/vnd.ez.api.VersionList+json",
                    "_href": "/api/ezp/v2/content/objects/60/versions"
                },
                "CurrentVersion": {
                    "_media-type": "application/vnd.ez.api.Version+json",
                    "_href": "/api/ezp/v2/content/objects/60/currentversion",
                    "Version": {
                        "_media-type": "application/vnd.ez.api.Version+json",
                        "_href": "/api/ezp/v2/content/objects/60/versions/6",
                        "VersionInfo": {
                            "id": 588,
                            "versionNo": 6,
                            "status": "PUBLISHED",
                            "modificationDate": "2015-11-28T00:36:08+01:00",
                            "Creator": {
                                "_media-type": "application/vnd.ez.api.User+json",
                                "_href": "/api/ezp/v2/user/users/14"
                            },
                            "creationDate": "2015-11-28T00:36:03+01:00",
                            "initialLanguageCode": "eng-GB",
                            "languageCodes": "eng-GB",
                            "VersionTranslationInfo": {
                                "_media-type": "application/vnd.ez.api.VersionTranslationInfo+json",
                                "Language": [
                                    {
                                        "languageCode": "eng-GB"
                                    }
                                ]
                            },
                            "names": {
                                "value": [
                                    {
                                        "_languageCode": "eng-GB",
                                        "#text": "Top Stories"
                                    }
                                ]
                            },
                            "Content": {
                                "_media-type": "application/vnd.ez.api.ContentInfo+json",
                                "_href": "/api/ezp/v2/content/objects/60"
                            }
                        },
                        "Fields": {
                            "field": [
                                {
                                    "id": 196,
                                    "fieldDefinitionIdentifier": "title",
                                    "languageCode": "eng-GB",
                                    "fieldTypeIdentifier": "ezstring",
                                    "fieldValue": "Top Stories"
                                },
                                {
                                    "id": 197,
                                    "fieldDefinitionIdentifier": "description",
                                    "languageCode": "eng-GB",
                                    "fieldTypeIdentifier": "ezrichtext",
                                    "fieldValue": {
                                        "xml": "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<section xmlns=\"http://docbook.org/ns/docbook\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" xmlns:ezxhtml=\"http://ez.no/xmlns/ezpublish/docbook/xhtml\" xmlns:ezcustom=\"http://ez.no/xmlns/ezpublish/docbook/custom\" version=\"5.0-variant ezpublish-1.0\"><para>How to get you idea onto the eZ blog</para></section>\n",
                                        "xhtml5edit": "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<section xmlns=\"http://ez.no/namespaces/ezpublish5/xhtml5/edit\"><p>How to get you idea onto the eZ blog</p></section>\n"
                                    }
                                },
                                {
                                    "id": 198,
                                    "fieldDefinitionIdentifier": "image",
                                    "languageCode": "eng-GB",
                                    "fieldTypeIdentifier": "ezimage",
                                    "fieldValue": {
                                        "id": "8/9/1/0/198-2-eng-GB/blog.jpg",
                                        "path": "/8/9/1/0/198-2-eng-GB/blog.jpg",
                                        "alternativeText": "",
                                        "fileName": "blog.jpg",
                                        "fileSize": 752160,
                                        "imageId": "60-198-6",
                                        "uri": "/var/site/storage/images/8/9/1/0/198-2-eng-GB/blog.jpg",
                                        "inputUri": null,
                                        "width": "1500",
                                        "height": "1000",
                                        "variations": {
                                            "ezplatform_admin_ui_profile_picture_user_menu": {
                                                "href": "/api/ezp/v2/content/binary/images/60-198-6/variations/ezplatform_admin_ui_profile_picture_user_menu"
                                            },
                                            "large": {
                                                "href": "/api/ezp/v2/content/binary/images/60-198-6/variations/large"
                                            },
                                            "medium": {
                                                "href": "/api/ezp/v2/content/binary/images/60-198-6/variations/medium"
                                            },
                                            "reference": {
                                                "href": "/api/ezp/v2/content/binary/images/60-198-6/variations/reference"
                                            },
                                            "small": {
                                                "href": "/api/ezp/v2/content/binary/images/60-198-6/variations/small"
                                            },
                                            "tiny": {
                                                "href": "/api/ezp/v2/content/binary/images/60-198-6/variations/tiny"
                                            }
                                        }
                                    }
                                }
                            ]
                        },
                        "Relations": {
                            "_media-type": "application/vnd.ez.api.RelationList+json",
                            "_href": "/api/ezp/v2/content/objects/60/versions/6/relations",
                            "Relation": []
                        }
                    }
                },
                "Section": {
                    "_media-type": "application/vnd.ez.api.Section+json",
                    "_href": "/api/ezp/v2/content/sections/1"
                },
                "MainLocation": {
                    "_media-type": "application/vnd.ez.api.Location+json",
                    "_href": "/api/ezp/v2/content/locations/1/2/62"
                },
                "Locations": {
                    "_media-type": "application/vnd.ez.api.LocationList+json",
                    "_href": "/api/ezp/v2/content/objects/60/locations"
                },
                "Owner": {
                    "_media-type": "application/vnd.ez.api.User+json",
                    "_href": "/api/ezp/v2/user/users/14"
                },
                "lastModificationDate": "2015-11-28T00:36:08+01:00",
                "publishedDate": "2015-11-28T00:36:08+01:00",
                "mainLanguageCode": "eng-GB",
                "currentVersionNo": 6,
                "alwaysAvailable": true,
                "ObjectStates": {
                    "_media-type": "application/vnd.ez.api.ContentObjectStates+json",
                    "_href": "/api/ezp/v2/content/objects/60/objectstates"
                }
            }
        },
        {
            "location": {
                "_media-type": "application/vnd.ez.api.Location+json",
                "_href": "/api/ezp/v2/content/locations/1/2/61",
                "id": 61,
                "priority": 20,
                "hidden": false,
                "invisible": false,
                "ParentLocation": {
                    "_media-type": "application/vnd.ez.api.Location+json",
                    "_href": "/api/ezp/v2/content/locations/1/2"
                },
                "pathString": "/1/2/61/",
                "depth": 2,
                "childCount": 53,
                "remoteId": "fa31454f371a62e5b006ef99b457398b",
                "Children": {
                    "_media-type": "application/vnd.ez.api.LocationList+json",
                    "_href": "/api/ezp/v2/content/locations/1/2/61/children"
                },
                "Content": {
                    "_media-type": "application/vnd.ez.api.Content+json",
                    "_href": "/api/ezp/v2/content/objects/59"
                },
                "sortField": "PATH",
                "sortOrder": "ASC",
                "UrlAliases": {
                    "_media-type": "application/vnd.ez.api.UrlAliasRefList+json",
                    "_href": "/api/ezp/v2/content/locations/1/2/61/urlaliases"
                },
                "ContentInfo": {
                    "_media-type": "application/vnd.ez.api.ContentInfo+json",
                    "_href": "/api/ezp/v2/content/objects/59",
                    "Content": {
                        "_media-type": "application/vnd.ez.api.ContentInfo+json",
                        "_href": "/api/ezp/v2/content/objects/59",
                        "_remoteId": "411b1d5cb865e902e6df47241910d50a",
                        "_id": 59,
                        "ContentType": {
                            "_media-type": "application/vnd.ez.api.ContentType+json",
                            "_href": "/api/ezp/v2/content/types/20"
                        },
                        "Name": "Projects",
                        "Versions": {
                            "_media-type": "application/vnd.ez.api.VersionList+json",
                            "_href": "/api/ezp/v2/content/objects/59/versions"
                        },
                        "CurrentVersion": {
                            "_media-type": "application/vnd.ez.api.Version+json",
                            "_href": "/api/ezp/v2/content/objects/59/currentversion"
                        },
                        "Section": {
                            "_media-type": "application/vnd.ez.api.Section+json",
                            "_href": "/api/ezp/v2/content/sections/1"
                        },
                        "Locations": {
                            "_media-type": "application/vnd.ez.api.LocationList+json",
                            "_href": "/api/ezp/v2/content/objects/59/locations"
                        },
                        "Owner": {
                            "_media-type": "application/vnd.ez.api.User+json",
                            "_href": "/api/ezp/v2/user/users/14"
                        },
                        "lastModificationDate": "2015-11-27T13:34:53+01:00",
                        "publishedDate": "2015-11-27T13:34:53+01:00",
                        "mainLanguageCode": "eng-GB",
                        "currentVersionNo": 2,
                        "alwaysAvailable": true,
                        "ObjectStates": {
                            "_media-type": "application/vnd.ez.api.ContentObjectStates+json",
                            "_href": "/api/ezp/v2/content/objects/59/objectstates"
                        }
                    }
                }
            },
            "content": {
                "_media-type": "application/vnd.ez.api.Content+json",
                "_href": "/api/ezp/v2/content/objects/59",
                "_remoteId": "411b1d5cb865e902e6df47241910d50a",
                "_id": 59,
                "ContentType": {
                    "_media-type": "application/vnd.ez.api.ContentType+json",
                    "_href": "/api/ezp/v2/content/types/20"
                },
                "Name": "Projects",
                "Versions": {
                    "_media-type": "application/vnd.ez.api.VersionList+json",
                    "_href": "/api/ezp/v2/content/objects/59/versions"
                },
                "CurrentVersion": {
                    "_media-type": "application/vnd.ez.api.Version+json",
                    "_href": "/api/ezp/v2/content/objects/59/currentversion",
                    "Version": {
                        "_media-type": "application/vnd.ez.api.Version+json",
                        "_href": "/api/ezp/v2/content/objects/59/versions/2",
                        "VersionInfo": {
                            "id": 507,
                            "versionNo": 2,
                            "status": "PUBLISHED",
                            "modificationDate": "2015-11-27T13:34:53+01:00",
                            "Creator": {
                                "_media-type": "application/vnd.ez.api.User+json",
                                "_href": "/api/ezp/v2/user/users/14"
                            },
                            "creationDate": "2015-11-27T13:34:49+01:00",
                            "initialLanguageCode": "eng-GB",
                            "languageCodes": "eng-GB",
                            "VersionTranslationInfo": {
                                "_media-type": "application/vnd.ez.api.VersionTranslationInfo+json",
                                "Language": [
                                    {
                                        "languageCode": "eng-GB"
                                    }
                                ]
                            },
                            "names": {
                                "value": [
                                    {
                                        "_languageCode": "eng-GB",
                                        "#text": "Projects"
                                    }
                                ]
                            },
                            "Content": {
                                "_media-type": "application/vnd.ez.api.ContentInfo+json",
                                "_href": "/api/ezp/v2/content/objects/59"
                            }
                        },
                        "Fields": {
                            "field": [
                                {
                                    "id": 194,
                                    "fieldDefinitionIdentifier": "title",
                                    "languageCode": "eng-GB",
                                    "fieldTypeIdentifier": "ezstring",
                                    "fieldValue": "Projects"
                                },
                                {
                                    "id": 195,
                                    "fieldDefinitionIdentifier": "description",
                                    "languageCode": "eng-GB",
                                    "fieldTypeIdentifier": "ezrichtext",
                                    "fieldValue": {
                                        "xml": "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<section xmlns=\"http://docbook.org/ns/docbook\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" xmlns:ezxhtml=\"http://ez.no/xmlns/ezpublish/docbook/xhtml\" xmlns:ezcustom=\"http://ez.no/xmlns/ezpublish/docbook/custom\" version=\"5.0-variant ezpublish-1.0\"><para>Check them out</para></section>\n",
                                        "xhtml5edit": "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<section xmlns=\"http://ez.no/namespaces/ezpublish5/xhtml5/edit\"><p>Check them out</p></section>\n"
                                    }
                                }
                            ]
                        },
                        "Relations": {
                            "_media-type": "application/vnd.ez.api.RelationList+json",
                            "_href": "/api/ezp/v2/content/objects/59/versions/2/relations",
                            "Relation": []
                        }
                    }
                },
                "Section": {
                    "_media-type": "application/vnd.ez.api.Section+json",
                    "_href": "/api/ezp/v2/content/sections/1"
                },
                "MainLocation": {
                    "_media-type": "application/vnd.ez.api.Location+json",
                    "_href": "/api/ezp/v2/content/locations/1/2/61"
                },
                "Locations": {
                    "_media-type": "application/vnd.ez.api.LocationList+json",
                    "_href": "/api/ezp/v2/content/objects/59/locations"
                },
                "Owner": {
                    "_media-type": "application/vnd.ez.api.User+json",
                    "_href": "/api/ezp/v2/user/users/14"
                },
                "lastModificationDate": "2015-11-27T13:34:53+01:00",
                "publishedDate": "2015-11-27T13:34:53+01:00",
                "mainLanguageCode": "eng-GB",
                "currentVersionNo": 2,
                "alwaysAvailable": true,
                "ObjectStates": {
                    "_media-type": "application/vnd.ez.api.ContentObjectStates+json",
                    "_href": "/api/ezp/v2/content/objects/59/objectstates"
                }
            }
        },
        {
            "location": {
                "_media-type": "application/vnd.ez.api.Location+json",
                "_href": "/api/ezp/v2/content/locations/1/2/60",
                "id": 60,
                "priority": 30,
                "hidden": false,
                "invisible": false,
                "ParentLocation": {
                    "_media-type": "application/vnd.ez.api.Location+json",
                    "_href": "/api/ezp/v2/content/locations/1/2"
                },
                "pathString": "/1/2/60/",
                "depth": 2,
                "childCount": 0,
                "remoteId": "11da938ffe35cd7e808213f3c6642e5a",
                "Children": {
                    "_media-type": "application/vnd.ez.api.LocationList+json",
                    "_href": "/api/ezp/v2/content/locations/1/2/60/children"
                },
                "Content": {
                    "_media-type": "application/vnd.ez.api.Content+json",
                    "_href": "/api/ezp/v2/content/objects/58"
                },
                "sortField": "PATH",
                "sortOrder": "ASC",
                "UrlAliases": {
                    "_media-type": "application/vnd.ez.api.UrlAliasRefList+json",
                    "_href": "/api/ezp/v2/content/locations/1/2/60/urlaliases"
                },
                "ContentInfo": {
                    "_media-type": "application/vnd.ez.api.ContentInfo+json",
                    "_href": "/api/ezp/v2/content/objects/58",
                    "Content": {
                        "_media-type": "application/vnd.ez.api.ContentInfo+json",
                        "_href": "/api/ezp/v2/content/objects/58",
                        "_remoteId": "c38f24389ad64e2dd43c8c057e4d64de",
                        "_id": 58,
                        "ContentType": {
                            "_media-type": "application/vnd.ez.api.ContentType+json",
                            "_href": "/api/ezp/v2/content/types/19"
                        },
                        "Name": "Contact Us",
                        "Versions": {
                            "_media-type": "application/vnd.ez.api.VersionList+json",
                            "_href": "/api/ezp/v2/content/objects/58/versions"
                        },
                        "CurrentVersion": {
                            "_media-type": "application/vnd.ez.api.Version+json",
                            "_href": "/api/ezp/v2/content/objects/58/currentversion"
                        },
                        "Section": {
                            "_media-type": "application/vnd.ez.api.Section+json",
                            "_href": "/api/ezp/v2/content/sections/1"
                        },
                        "Locations": {
                            "_media-type": "application/vnd.ez.api.LocationList+json",
                            "_href": "/api/ezp/v2/content/objects/58/locations"
                        },
                        "Owner": {
                            "_media-type": "application/vnd.ez.api.User+json",
                            "_href": "/api/ezp/v2/user/users/14"
                        },
                        "lastModificationDate": "2015-11-27T13:59:57+01:00",
                        "publishedDate": "2015-11-27T13:59:57+01:00",
                        "mainLanguageCode": "eng-GB",
                        "currentVersionNo": 2,
                        "alwaysAvailable": true,
                        "ObjectStates": {
                            "_media-type": "application/vnd.ez.api.ContentObjectStates+json",
                            "_href": "/api/ezp/v2/content/objects/58/objectstates"
                        }
                    }
                }
            },
            "content": {
                "_media-type": "application/vnd.ez.api.Content+json",
                "_href": "/api/ezp/v2/content/objects/58",
                "_remoteId": "c38f24389ad64e2dd43c8c057e4d64de",
                "_id": 58,
                "ContentType": {
                    "_media-type": "application/vnd.ez.api.ContentType+json",
                    "_href": "/api/ezp/v2/content/types/19"
                },
                "Name": "Contact Us",
                "Versions": {
                    "_media-type": "application/vnd.ez.api.VersionList+json",
                    "_href": "/api/ezp/v2/content/objects/58/versions"
                },
                "CurrentVersion": {
                    "_media-type": "application/vnd.ez.api.Version+json",
                    "_href": "/api/ezp/v2/content/objects/58/currentversion",
                    "Version": {
                        "_media-type": "application/vnd.ez.api.Version+json",
                        "_href": "/api/ezp/v2/content/objects/58/versions/2",
                        "VersionInfo": {
                            "id": 511,
                            "versionNo": 2,
                            "status": "PUBLISHED",
                            "modificationDate": "2015-11-27T13:59:57+01:00",
                            "Creator": {
                                "_media-type": "application/vnd.ez.api.User+json",
                                "_href": "/api/ezp/v2/user/users/14"
                            },
                            "creationDate": "2015-11-27T13:59:53+01:00",
                            "initialLanguageCode": "eng-GB",
                            "languageCodes": "eng-GB",
                            "VersionTranslationInfo": {
                                "_media-type": "application/vnd.ez.api.VersionTranslationInfo+json",
                                "Language": [
                                    {
                                        "languageCode": "eng-GB"
                                    }
                                ]
                            },
                            "names": {
                                "value": [
                                    {
                                        "_languageCode": "eng-GB",
                                        "#text": "Contact Us"
                                    }
                                ]
                            },
                            "Content": {
                                "_media-type": "application/vnd.ez.api.ContentInfo+json",
                                "_href": "/api/ezp/v2/content/objects/58"
                            }
                        },
                        "Fields": {
                            "field": [
                                {
                                    "id": 189,
                                    "fieldDefinitionIdentifier": "title",
                                    "languageCode": "eng-GB",
                                    "fieldTypeIdentifier": "ezstring",
                                    "fieldValue": "Contact Us"
                                },
                                {
                                    "id": 190,
                                    "fieldDefinitionIdentifier": "description",
                                    "languageCode": "eng-GB",
                                    "fieldTypeIdentifier": "ezrichtext",
                                    "fieldValue": {
                                        "xml": "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<section xmlns=\"http://docbook.org/ns/docbook\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" xmlns:ezxhtml=\"http://ez.no/xmlns/ezpublish/docbook/xhtml\" xmlns:ezcustom=\"http://ez.no/xmlns/ezpublish/docbook/custom\" version=\"5.0-variant ezpublish-1.0\"><para>Want to know more, get a quote or schedule a demo?</para><para>Please tell us more about your needs filling the form beside.</para></section>\n",
                                        "xhtml5edit": "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<section xmlns=\"http://ez.no/namespaces/ezpublish5/xhtml5/edit\"><p>Want to know more, get a quote or schedule a demo?</p><p>Please tell us more about your needs filling the form beside.</p></section>\n"
                                    }
                                },
                                {
                                    "id": 191,
                                    "fieldDefinitionIdentifier": "image",
                                    "languageCode": "eng-GB",
                                    "fieldTypeIdentifier": "ezimage",
                                    "fieldValue": {
                                        "id": "1/9/1/0/191-2-eng-GB/contact_form.jpg",
                                        "path": "/1/9/1/0/191-2-eng-GB/contact_form.jpg",
                                        "alternativeText": "",
                                        "fileName": "contact_form.jpg",
                                        "fileSize": 216871,
                                        "imageId": "58-191-2",
                                        "uri": "/var/site/storage/images/1/9/1/0/191-2-eng-GB/contact_form.jpg",
                                        "inputUri": null,
                                        "width": "800",
                                        "height": "532",
                                        "variations": {
                                            "ezplatform_admin_ui_profile_picture_user_menu": {
                                                "href": "/api/ezp/v2/content/binary/images/58-191-2/variations/ezplatform_admin_ui_profile_picture_user_menu"
                                            },
                                            "large": {
                                                "href": "/api/ezp/v2/content/binary/images/58-191-2/variations/large"
                                            },
                                            "medium": {
                                                "href": "/api/ezp/v2/content/binary/images/58-191-2/variations/medium"
                                            },
                                            "reference": {
                                                "href": "/api/ezp/v2/content/binary/images/58-191-2/variations/reference"
                                            },
                                            "small": {
                                                "href": "/api/ezp/v2/content/binary/images/58-191-2/variations/small"
                                            },
                                            "tiny": {
                                                "href": "/api/ezp/v2/content/binary/images/58-191-2/variations/tiny"
                                            }
                                        }
                                    }
                                },
                                {
                                    "id": 192,
                                    "fieldDefinitionIdentifier": "email",
                                    "languageCode": "eng-GB",
                                    "fieldTypeIdentifier": "ezemail",
                                    "fieldValue": "no-spam@ez.no"
                                },
                                {
                                    "id": 193,
                                    "fieldDefinitionIdentifier": "location",
                                    "languageCode": "eng-GB",
                                    "fieldTypeIdentifier": "ezgmaplocation",
                                    "fieldValue": {
                                        "latitude": 40.711056,
                                        "longitude": -73.935836,
                                        "address": "US, NY, 35 Meadow Street, suite 103"
                                    }
                                }
                            ]
                        },
                        "Relations": {
                            "_media-type": "application/vnd.ez.api.RelationList+json",
                            "_href": "/api/ezp/v2/content/objects/58/versions/2/relations",
                            "Relation": []
                        }
                    }
                },
                "Section": {
                    "_media-type": "application/vnd.ez.api.Section+json",
                    "_href": "/api/ezp/v2/content/sections/1"
                },
                "MainLocation": {
                    "_media-type": "application/vnd.ez.api.Location+json",
                    "_href": "/api/ezp/v2/content/locations/1/2/60"
                },
                "Locations": {
                    "_media-type": "application/vnd.ez.api.LocationList+json",
                    "_href": "/api/ezp/v2/content/objects/58/locations"
                },
                "Owner": {
                    "_media-type": "application/vnd.ez.api.User+json",
                    "_href": "/api/ezp/v2/user/users/14"
                },
                "lastModificationDate": "2015-11-27T13:59:57+01:00",
                "publishedDate": "2015-11-27T13:59:57+01:00",
                "mainLanguageCode": "eng-GB",
                "currentVersionNo": 2,
                "alwaysAvailable": true,
                "ObjectStates": {
                    "_media-type": "application/vnd.ez.api.ContentObjectStates+json",
                    "_href": "/api/ezp/v2/content/objects/58/objectstates"
                }
            }
        }
    ];

    listContainers.forEach(container => {
        ReactDOM.render(React.createElement(eZ.modules.SubItems, {
            parentLocationId: container.dataset.location,
            sortClauses: {[sortField]: sortOrder},
            restInfo: {token, siteaccess},
            // @TODO
            // discover content location view URL from backend routes
            locationViewLink: '/admin/content/location/{{locationId}}',
            extraActions: [{
                component: eZ.modules.MultiFileUpload,
                attrs: Object.assign({}, mfuAttrs, {
                    onPopupClose: (itemsUploaded) => {
                        if (itemsUploaded.length) {
                            window.location.reload(true);
                        }
                    },
                    popupOnly: false,
                    asButton: true
                })
            }],
            // items
        }), container);
    });
})();
