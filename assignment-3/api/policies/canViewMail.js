const isAdmin = require("./isAdmin");
const ownsResource = require("./ownsResource");


// True if user is admin or owns resource
module.exports = function canViewMail(user, mail) {
  return isAdmin(user) || ownsResource(user, mail);
};