class ApiError extends Error {
    constructor(error, message, statusCode) {
        super(message);

        this.error = error;
        this.statusCode = statusCode;
        this.timestamp = new Date().toISOString();
    }
}

module.exports = ApiError;
