# GitHub Issue Template for PHASE Tasks

## Template Structure

```markdown
## 🎯 Task Overview
[Task description from the phase document]

## ⏱️ Estimated Time: [X hours (Y minutes)]

## 🎯 Success Criteria
[Success criteria from the phase document]

## 📁 Files to be modified/created:
- [List of files from the phase document]

## 🔗 Dependencies
- [Dependencies from the phase document]

## 📊 Phase
[Phase section and total hours]

## 📋 Implementation Checklist

[For each Implementation item, create a ### heading with atomic sub-items]

### ✅ [Implementation Item Title]

- [ ] [Atomic sub-task 1 - specific, actionable, 15-30 minutes]
- [ ] [Atomic sub-task 2 - specific, actionable, 15-30 minutes]
- [ ] [Atomic sub-task 3 - specific, actionable, 15-30 minutes]
- [ ] [Atomic sub-task 4 - specific, actionable, 15-30 minutes]
- [ ] [Atomic sub-task 5 - specific, actionable, 15-30 minutes]

### ✅ [Next Implementation Item Title]

- [ ] [Atomic sub-task 1]
- [ ] [Atomic sub-task 2]
- [ ] [Atomic sub-task 3]
- [ ] [Atomic sub-task 4]

[Continue for all Implementation items...]

## 🏷️ Labels
`phase-3`, `[feature-category]`, `[technology]`, `[component]`
```

## Guidelines for Atomic Sub-tasks

Each atomic sub-task should be:

- **Specific**: Clear about what needs to be done
- **Actionable**: Developer can start immediately
- **Testable**: Clear completion criteria
- **Time-boxed**: 15-30 minutes maximum
- **Independent**: Can be completed without waiting for other sub-tasks

## Example Atomic Sub-tasks

### Bad Examples (too vague/large)

- [ ] Implement authentication
- [ ] Add validation
- [ ] Create UI

### Good Examples (atomic and specific)

- [ ] Create `validateSAMLMetadata()` function with XML schema validation
- [ ] Add RSA signature verification using node-forge library
- [ ] Build metadata expiration check with 24-hour refresh logic
- [ ] Create error handling for malformed XML with specific error codes
- [ ] Add unit tests for metadata parser with 5 test cases
- [ ] Implement certificate chain validation against root CA

## Technology-Specific Examples

### Database Tasks

- [ ] Create `saml_configurations` table with 8 required columns
- [ ] Add database migration script for organization-scoped SAML settings
- [ ] Create Prisma model with proper relationships and indexes
- [ ] Add database seed script with sample SAML configurations

### API Tasks

- [ ] Create POST `/api/auth/saml/acs` endpoint with request validation
- [ ] Add Zod schema for SAML assertion validation
- [ ] Implement NextAuth SAML provider integration
- [ ] Add SAML logout endpoint with proper session cleanup

### UI Tasks

- [ ] Create `SamlConfigForm` component with 6 input fields
- [ ] Add form validation using react-hook-form and Zod
- [ ] Implement file upload for IdP metadata with drag-and-drop
- [ ] Add loading states and error handling for SAML configuration

### Testing Tasks

- [ ] Create unit tests for SAML metadata parser (5 test cases)
- [ ] Add integration tests for SAML authentication flow
- [ ] Create E2E test for complete SAML login process
- [ ] Add performance tests for metadata processing under load
