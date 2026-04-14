const express = require("express");
const jwt = require("jsonwebtoken");
const users = require("../data/users");
const ApiError = require("../middleware/ApiError");

const router = express.Router();
const SECRET = process.env.JWT_SECRET || "CHANGE_ME_BEFORE_SUBMISSION";

router.post("/login", (req, res, next) => {
    try {
        const { username, password } = req.body;

        if (!username || !password) {
            return next(
                new ApiError("BadRequest", "Missing Credentials", 400),
            );
        }

        const user = users.find((u) => u.username === username);

        if (!user || user.password !== password) {
            return next(
                new ApiError("Unauthorized", "Invalid Credentials", 401),
            );
        }

        const token = jwt.sign(
            {
                userId: user.id,
                role: user.role,
            },
            SECRET,
            { expiresIn: "1h" },
        );

        res.json({ token: token });
    } catch (err) {
        next(err);
    }
});

module.exports = router;
