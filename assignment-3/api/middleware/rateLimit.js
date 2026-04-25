const ApiError = require("../middleware/ApiError");
const windowMs =
    (parseInt(process.env.RATE_LIMIT_WINDOW_SECONDS, 10) || 10) * 1000;
const maxRequests = parseInt(process.env.RATE_LIMIT_MAX, 10) || 5;

const buckets = new Map();
// shape: key -> { count, windowStart }

module.exports = function rateLimit(req, res, next) {
    const key = req.ip;
    const now = Date.now();

    const userData = buckets.get(key);

    // If this is user's first req, set the bucket
    if (!userData || now - userData.windowStart > windowMs) {
        buckets.set(key, {
            count: 1,
            windowStart: now,
        });
        return next();
    }

    // Update the request counter
    userData.count += 1;

    // If counter exceeds the limit
    if (userData.count > maxRequests) {
        const msLeft = userData.windowStart + windowMs - now;
        const retryAfterSeconds = Math.ceil(msLeft / 1000);

        const error = new ApiError(
            "TooManyRequests",
            "Rate limit exceeded",
            429,
        );

        error.retryAfter = retryAfterSeconds;

        return next(error);
    }

    next();
};
