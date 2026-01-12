# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-01-12

### Added
- Initial release of TAP Payment PHP SDK
- Uses official CDN-hosted JavaScript SDK from unpkg
- Transaction parameter validation with helpful error messages
- Multiple integration methods:
  - `quickPayment()` - One-liner for quick integration
  - Manual setup with `setup()` and `render()`
  - SDK-only mode with `renderSDKOnly()` for dynamic JavaScript usage
- Support for custom payloads via `customPayload` parameter
- Save payment details functionality with `savePaymentDetails`
- Callback and onClose event handlers for payment lifecycle
- Customizable CDN URL via `setCdnURL()`
- JSON export of transaction parameters via `getTransactionParamsJSON()`
- GitHub Actions workflow for automated releases
