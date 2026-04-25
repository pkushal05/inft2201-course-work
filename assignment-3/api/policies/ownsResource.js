// Returns true if mail.userId === user.userId.

module.exports = function ownsResource(user, mail) {
  return user.userId === mail.userId;
};