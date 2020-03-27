import { handleRequestResponse } from '../../common/helpers/request.helper';
import { showErrorNotification } from '../../common/services/notification.service';

const ENDPOINT_LOAD_SUBITEMS = '/api/ezp/v2/location/tree/load-subitems';
const ENDPOINT_LOAD_SUBTREE = '/api/ezp/v2/location/tree/load-subtree';

export const loadLocationItems = ({ siteaccess }, parentLocationId, callback, limit = 50, offset = 0) => {
    const request = new Request(`${ENDPOINT_LOAD_SUBITEMS}/${parentLocationId}/${limit}/${offset}`, {
        method: 'GET',
        mode: 'same-origin',
        credentials: 'same-origin',
        headers: {
            Accept: 'application/vnd.ez.api.ContentTreeNode+json',
            'X-Siteaccess': siteaccess,
        },
    });

    fetch(request)
        .then(handleRequestResponse)
        .then((data) => {
            const location = data.ContentTreeNode;

            location.children = location.children.map(mapChildrenToSubitems);

            return mapChildrenToSubitems(location);
        })
        .then(callback)
        .catch(showErrorNotification);
};

export const loadSubtree = ({ token, siteaccess }, subtree, callback) => {
    const request = new Request(`${ENDPOINT_LOAD_SUBTREE}`, {
        method: 'POST',
        mode: 'same-origin',
        credentials: 'same-origin',
        body: JSON.stringify({
            LoadSubtreeRequest: {
                '_media-type': 'application/vnd.ez.api.ContentTreeLoadSubtreeRequest',
                nodes: subtree,
            },
        }),
        headers: {
            Accept: 'application/vnd.ez.api.ContentTreeRoot+json',
            'Content-Type': 'application/vnd.ez.api.ContentTreeLoadSubtreeRequest+json',
            'X-Siteaccess': siteaccess,
            'X-CSRF-Token': token,
        },
    });

    fetch(request)
        .then(handleRequestResponse)
        .then((data) => {
            const loadedSubtree = data.ContentTreeRoot.ContentTreeNodeList;

            return mapChildrenToSubitemsDeep(loadedSubtree);
        })
        .then(callback)
        .catch(showErrorNotification);
};

const mapChildrenToSubitemsDeep = (tree) =>
    tree.map((subtree) => {
        mapChildrenToSubitems(subtree);
        subtree.subitems = mapChildrenToSubitemsDeep(subtree.subitems);

        return subtree;
    });

const mapChildrenToSubitems = (location) => {
    location.totalSubitemsCount = location.totalChildrenCount;
    location.subitems = location.children;

    delete location.totalChildrenCount;
    delete location.children;
    delete location.displayLimit;

    return location;
};
