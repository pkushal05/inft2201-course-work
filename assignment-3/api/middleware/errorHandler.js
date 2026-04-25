// Structured Error handler

module.exports = function errorHandler(err, req, res, next) {

  const statusCode = err.statusCode || 500;

  let response = {
    error: err.error || "Unexpected Error Occured",
    message: err.message,
    statusCode: statusCode,
    requestId: req.requestId || null,
    timestamp: err.timestamp || new Date().toISOString(),
  };

  if (err.retryAfter) {
    res.set("Retry-After", err.retryAfter);
    response = Object.assign(response, { retryAfter: err.retryAfter });
  };
  res.status(statusCode).json(response);
};