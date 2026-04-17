```markdown
# bml-connect-php Development Patterns

> Auto-generated skill from repository analysis

## Overview
This skill teaches you the core development patterns and conventions used in the `bml-connect-php` TypeScript codebase. You'll learn how to structure files, write and organize code, follow commit conventions, and implement and run tests according to the repository's standards.

## Coding Conventions

### File Naming
- Use **PascalCase** for all file names.
  - Example: `UserService.ts`, `ApiClient.ts`

### Import Style
- Use **relative imports** to reference other modules.
  - Example:
    ```typescript
    import { User } from './User';
    ```

### Export Style
- Use **named exports** for all modules.
  - Example:
    ```typescript
    export function connect() { ... }
    export const VERSION = '1.0.0';
    ```

### Commit Messages
- Use **Conventional Commits** with the `feat` prefix for new features.
  - Example:
    ```
    feat: add user authentication to ApiClient
    ```

## Workflows

*No automated workflows detected in this repository.*

## Testing Patterns

- **Framework:** Not explicitly detected.
- **Test File Pattern:** Test files are named with the pattern `*.test.*`.
  - Example: `UserService.test.ts`
- **Test Structure:** Place test files alongside or near the code they test, following the naming pattern.

#### Example Test File
```typescript
// UserService.test.ts
import { getUser } from './UserService';

test('should fetch user by ID', () => {
  const user = getUser(1);
  expect(user.id).toBe(1);
});
```

## Commands
| Command | Purpose |
|---------|---------|
| /test   | Run all test files matching `*.test.*` |
| /commit | Create a conventional commit (e.g., `feat: ...`) |
```
