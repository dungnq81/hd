let ensemble = require('ensemble-social-share/dist/js/ensemble-social-share.min');
const options = {
    displays: ['facebook', 'ex', 'whatsapp', 'messenger', 'telegram', 'linkedin', 'send-email', 'copy-link', 'web-share'],
};
const element = document.querySelector('.social-share');
if (element) {
    new ensemble.SocialShare(element, options);
}
