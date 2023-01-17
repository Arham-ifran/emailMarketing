import * as Constants from "../../constants";

const GetAuthMetaData = (auth_id) => {
    var largeImage = '';
    var smallImage = '';
    var name = '';
    var slug = '';

    switch (auth_id) {
        case Constants.AUTH_TYPE.GOOGLE:
            largeImage = '/images/googledrive.png';
            smallImage = '/images/small-googledrive.png';
            name = 'Google';//t('google_drive');
            slug = 'google';
            break;
        case Constants.AUTH_TYPE.FACEBOOK:
            largeImage = '/images/dropbox.png';
            smallImage = '/images/small-dropbox.png';
            name = 'Facebook';//t('dropbox');
            slug = 'facebook';
            break;
            case Constants.AUTH_TYPE.TWITTER:
            largeImage = '/images/onedrive1.png';
            smallImage = '/images/small-onedrive1.png';
            name = 'Twitter';//t('one_drive');
            slug = 'twitter';
            break;
    }

    return {
        'largeImage': largeImage,
        'smallImage': smallImage,
        'name': name,
        'slug': slug
    }
};

export default GetAuthMetaData;
