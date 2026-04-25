const jwt = require("jsonwebtoken");
const ApiError = require("./ApiError");

const SECRET = process.env.JWT_SECRET;

module.exports = function authenticateJWT(req, res, next) {

  // Get the header
  const header = req.headers.authorization;

  // If there's not header, throw Error
  if(!header) {
    return next(new ApiError("Forbidden", "Missing authorization headers", 403));
  }

  // Split the token
  const token = header.split(" ")[1];

  try {
    // Decode the jwt
    const decoded = jwt.verify(token, SECRET);
    // Attach it to the request
    req.user = decoded;
    next();
  } catch (err) {
    return next(new ApiError("Forbidden", "Invalid or Expired Token", 403));
  }
};