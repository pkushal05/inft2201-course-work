const { v4: uuidv4 } = require("uuid");

module.exports = function requestLogger(req, res, next) {

  const requestId = uuidv4();

  req.requestId = requestId;

  // Logging in console on every action
  console.log(`${req.method}, ${req.path}, Req-Id: ${req.requestId}`);

  next();
};