const ApiError = require("./ApiError");

module.exports = function authorize(policy) {
    return (req, res, next) => {
        try {
            const user = req.user;
            const mail = req.mail;

            if (!user || !mail) {
                return next(new ApiError("BadRequest", "Missing Data", 422));
            }

            // Only move ahead if the policy is satisfied
            if (policy(user, mail)) {
                next();
            } else {
                return next(
                    new ApiError(
                        "Unauthorized",
                        "You do not have permission to access this resource",
                        401,
                    ),
                );
            }
        } catch (err) {
            next(err);
        }
    };
};
