# Adopt GitHub MCP for Git/GitHub operations

## Overview

Adopt the GitHub Model-Context-Protocol (MCP) or equivalent GitHub-integrated tooling for any future Git/GitHub operations performed by the coding agent. This ensures all repository and PR related actions follow a consistent protocol, have proper authorization, and can be audited.

## Objective

- Use GitHub MCP for branch, commit, PR and workflow management when the coding agent interacts with GitHub or executes Git operations that affect the remote repository.

## Tasks

- Update internal processes to call GitHub MCP for any GitHub PR/issue/branch operations.
- Document how the MCP will be used and when manual approval is required.
- Add a minimal test/practice run plan (non-destructive) to validate MCP-based operations.

## Acceptance Criteria

- A documented process exists describing when and how GitHub MCP will be used.
- The coding agent creates commits and PRs only after MCP-based approval or when explicitly authorized.
- A sample non-destructive MCP operation plan is included.

## Notes

This is an operational/process task and does not require code changes by default. If integration code is needed later (MCP clients, tokens), create follow-up implementation tasks.
