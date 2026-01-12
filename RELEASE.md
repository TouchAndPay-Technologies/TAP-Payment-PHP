# GitHub Release Configuration
#
# This file documents how to create releases for this project.
# Releases allow consumers to download the SDK directly without cloning the repo.
#
# How to Create a Release:
#
# 1. Using GitHub CLI (recommended):
#    gh release create v1.0.0 \
#      --title "v1.0.0 - Initial Release" \
#      --notes-file CHANGELOG.md \
#      TAPPaymentPop.php \
#      example.php
#
# 2. Using GitHub Web UI:
#    a. Go to https://github.com/nicaborwn/TAP-Payment-PHP/releases/new
#    b. Choose a tag (e.g., v1.0.0)
#    c. Set release title
#    d. Add release notes (copy from CHANGELOG.md)
#    e. Attach files:
#       - TAPPaymentPop.php (main SDK file)
#       - example.php (usage example)
#    f. Publish release
#
# Release Assets:
# - TAPPaymentPop.php: The main SDK file consumers need
# - example.php: Example implementation for reference
# - Source code: Automatically included by GitHub
#
# Versioning:
# - Use Semantic Versioning (MAJOR.MINOR.PATCH)
# - Tag format: v2.0.0
