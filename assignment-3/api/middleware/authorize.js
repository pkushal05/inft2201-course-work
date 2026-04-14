// Generic authorization middleware that accepts a policy function.
// The policy function will receive (user, resource) and must return true/false.

const ApiError = require("./ApiError");

module.exports = function authorize(policy) {
    return (req, res, next) => {
        // TODO: implement:
        // - Read req.user and req.mail (or other resource, depending on route).
        // - If policy(user, resource) === true, call next().
        // - Otherwise, create an appropriate "Forbidden" error and pass to next(err).

        try {
            const user = req.user;
            const mail = req.mail;

            if (!user || !mail) {
                return next(
                    new ApiError("BadRequest", "Missing Data", 422),
                );
            }

            if (policy(user, mail)) {
                next();
            } else {
                return next(
                    new ApiError(
                        "Forbidden",
                        "You do not have permission to access this resource",
                        403,
                    ),
                );
            }
        } catch (err) {
            next(err);
        }
    };
};
