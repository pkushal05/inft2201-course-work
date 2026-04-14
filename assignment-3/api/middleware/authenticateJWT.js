const jwt = require("jsonwebtoken");
const ApiError = require("./ApiError");

const SECRET = process.env.JWT_SECRET;

// TODO: Implement authenticateJWT middleware for Assignment 3.
// Requirements:
// - Read the Authorization header: "Bearer <token>".
// - Verify the token using jwt.verify and SECRET.
// - If valid, attach the decoded payload to req.user.
// - If missing/invalid/expired, pass an appropriate error into next(err)
//   (do NOT send the response directly here — let errorHandler.js do that).

module.exports = function authenticateJWT(req, res, next) {

  const header = req.headers.authorization;

  if(!header) {
    return next(new ApiError("Forbidden", "Missing authorization headers", 403));
  }

  const token = header.split(" ")[1];

  try {
    const decoded = jwt.verify(token, SECRET);
    req.user = decoded;
    next();
  } catch (err) {
    return next(new ApiError("Forbidden", "Invalid or Expired Token", 403));
  }
};