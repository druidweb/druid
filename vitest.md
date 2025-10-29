# Progressive Test Coverage Improvement Prompt for Augment Code

## CRITICAL RULES - DO NOT VIOLATE:

- **NEVER delete or modify existing test files**
- **NEVER remove existing test methods**
- **ONLY add new tests or enhance existing ones by adding new test methods**
- **Always preserve all existing test code**

## TEST QUALITY REQUIREMENTS - MANDATORY:

- **ALL tests MUST PASS** - No failing, warning, risky, or skipped tests allowed
- **Tests must be meaningful** - Actually test component behavior, not just render without errors
- **Use proper assertions** - verify expected outcomes, user interactions, props, events, slots
- **Test edge cases and error conditions** - don't just test happy paths
- **Each test should test ONE specific behavior or interaction**
- **Use descriptive test names** that explain what component behavior is being tested

## LEVERAGE TEST INFRASTRUCTURE - REQUIRED:

- **Use vitest.setup.ts for reusable functions** - Don't repeat setup code in every test
- **Enhance test utilities** - Create robust component mounting helpers and shared test utilities
- **Create helper functions** for common patterns (mounting with props, mocking stores, event testing)
- **Use factories/fixtures** instead of manual object creation in each test
- **Avoid risky tests** by using proper test infrastructure, not reinventing in each test

### Examples of Good Test Infrastructure:

**In tests/vitest.setup.ts or test-utils.ts:**

```typescript
import { mount, VueWrapper } from '@vue/test-utils';
import { createPinia } from 'pinia';
import type { ComponentMountingOptions } from '@vue/test-utils';

export function createTestPinia() {
  return createPinia();
}

export function mountComponent<T>(component: T, options: ComponentMountingOptions<T> = {}): VueWrapper {
  return mount(component, {
    global: {
      plugins: [createTestPinia()],
      ...options.global,
    },
    ...options,
  });
}

export function createMockProps(overrides = {}) {
  return {
    title: 'Test Component',
    isVisible: true,
    items: [],
    ...overrides,
  };
}

export async function triggerAsyncAction(wrapper: VueWrapper, action: () => Promise<void>) {
  await action();
  await wrapper.vm.$nextTick();
}
```

**In component tests (using helpers):**

```typescript
test('renders with default props', () => {
  const wrapper = mountComponent(MyComponent, {
    props: createMockProps(),
  });
  // Clean, reusable test code
});
```

## VERIFICATION BEFORE MILESTONE COMMIT:

**BEFORE committing, you MUST verify:**

1. `bun run test:js` - ALL tests pass (green)
2. `bun run test:js:coverage` - Coverage target reached AND all tests pass
3. **Zero failures, warnings, risky, or skipped tests**
4. **If ANY test fails, fix it before proceeding**

## PROGRESSIVE MILESTONE SYSTEM:

**Current minimum threshold: 98.97%**
**Next milestone target: 100%**
**Final goal: 100%**

## WORKFLOW:

1. **PRIORITY: Focus on 0% coverage components first** - Always target completely untested components before improving existing test coverage
2. **Add tests incrementally** until coverage crosses the current milestone target
3. **Verify all tests pass** with `bun run test:js`
4. **When milestone is reached AND all tests pass:**
   - Run `bun run test:js:coverage` to verify coverage AND test success
   - Report: "Milestone reached: X.X% coverage with all tests passing."
   - **Ask: "Should I commit these changes? (Please verify with your own test run first)"**
   - **WAIT for human approval ("yes" or "no")**
5. **If approved:** Commit with `git add . && git commit -m "chore: upgrade component test coverage to X.X%"`
6. **Human will update thresholds, then say "continue"**
7. **Resume testing until next milestone**

## MILESTONES PROGRESSION (update as you go):

- [x] 3.17% → 25%
- [x] 28.99% → 50%
- [x] 61.57% → 75%
- [ ] 98.97% → 100%

## INSTRUCTIONS:

1. **PRIORITIZE 0% coverage components** - Always target completely untested components first for maximum coverage impact
2. **Set up proper test infrastructure FIRST** - Enhance test utilities and add helper functions before writing individual tests
3. **Analyze uncovered code** - identify untested components/methods/branches, starting with 0% files
4. **Write QUALITY component tests** - meaningful assertions that verify component behavior, props, events, slots
5. **Use reusable helpers** - Don't repeat mounting/setup code, leverage test utilities
6. **One component at a time** - work on single components systematically
7. **Ensure tests pass** with `bun run test:js` after each addition
8. **Check coverage** with `bun run test:js:coverage`
9. **Only ask for commit approval when ALL tests pass AND milestone reached**

## GOOD COMPONENT TEST EXAMPLES:

```typescript
// ✅ GOOD - Uses helper functions and tests props
test('renders correctly with props', () => {
  const props = createMockProps({ title: 'Custom Title', isVisible: true });
  const wrapper = mountComponent(MyComponent, { props });

  expect(wrapper.find('[data-testid="title"]').text()).toBe('Custom Title');
  expect(wrapper.find('.component').isVisible()).toBe(true);
});

// ✅ GOOD - Tests user interactions and events
test('emits click event when button is clicked', async () => {
  const wrapper = mountComponent(MyComponent);

  await wrapper.find('[data-testid="submit-btn"]').trigger('click');

  expect(wrapper.emitted('submit')).toBeTruthy();
  expect(wrapper.emitted('submit')[0]).toEqual(['submitted']);
});

// ✅ GOOD - Tests conditional rendering with different prop states
test('shows error message when hasError prop is true', () => {
  const wrapper = mountComponent(MyComponent, {
    props: createMockProps({ hasError: true, errorMessage: 'Something went wrong' }),
  });

  expect(wrapper.find('[data-testid="error"]').exists()).toBe(true);
  expect(wrapper.find('[data-testid="error"]').text()).toBe('Something went wrong');
});

// ✅ GOOD - Tests slots
test('renders slot content correctly', () => {
  const wrapper = mountComponent(MyComponent, {
    slots: {
      default: '<div data-testid="slot-content">Custom Content</div>',
    },
  });

  expect(wrapper.find('[data-testid="slot-content"]').text()).toBe('Custom Content');
});

// ✅ GOOD - Tests lifecycle and reactive data
test('updates reactive data when prop changes', async () => {
  const wrapper = mountComponent(MyComponent, {
    props: createMockProps({ count: 5 }),
  });

  expect(wrapper.find('[data-testid="count"]').text()).toBe('5');

  await wrapper.setProps({ count: 10 });

  expect(wrapper.find('[data-testid="count"]').text()).toBe('10');
});
```

## BAD COMPONENT TEST EXAMPLES:

```typescript
// ❌ BAD - Just mounts component without meaningful assertions
test('some test', () => {
  const wrapper = mountComponent(MyComponent);
  // No assertions about component behavior!
});

// ❌ BAD - Repeating setup code in every test (should be in test utilities)
test('component test 1', () => {
  const pinia = createPinia();
  const wrapper = mount(MyComponent, {
    global: { plugins: [pinia] },
    props: { title: 'Test', isVisible: true },
  });
  // ... test code
});

test('component test 2', () => {
  const pinia = createPinia(); // Repeated setup!
  const wrapper = mount(MyComponent, {
    global: { plugins: [pinia] }, // Repeated setup!
    props: { title: 'Test', isVisible: true }, // Repeated setup!
  });
  // ... test code
});

// ❌ BAD - Skipped or incomplete tests
test.skip('todo: write this test', () => {
  // Will write later - NO! Write it now
});

// ❌ BAD - Testing implementation details instead of behavior
test('bad test', () => {
  const wrapper = mountComponent(MyComponent);
  expect(wrapper.vm.internalMethod).toBeDefined(); // Testing internal implementation!
});
```

## COMPONENT TESTING AREAS TO COVER:

### Essential Test Categories:

1. **Props Testing** - All prop combinations, default values, required props
2. **Event Emission** - All emitted events with correct payloads
3. **User Interactions** - Clicks, form inputs, keyboard events
4. **Conditional Rendering** - Different states based on props/data
5. **Slots** - Default and named slots with various content
6. **Lifecycle Hooks** - mounted, updated, unmounted behaviors
7. **Computed Properties** - Different input scenarios
8. **Watchers** - Reactive changes and side effects
9. **Error States** - How component handles errors
10. **Accessibility** - ARIA attributes, keyboard navigation

### Vue-Specific Patterns:

```typescript
// Props validation
test('validates required props', () => {
  expect(() => mountComponent(MyComponent, { props: {} })).toThrow(); // or check console warnings
});

// V-model testing
test('supports v-model', async () => {
  const wrapper = mountComponent(MyComponent, {
    props: { modelValue: 'initial' },
  });

  await wrapper.find('input').setValue('updated');

  expect(wrapper.emitted('update:modelValue')).toBeTruthy();
  expect(wrapper.emitted('update:modelValue')[0]).toEqual(['updated']);
});

// Async component testing
test('handles async operations', async () => {
  const wrapper = mountComponent(AsyncComponent);

  await triggerAsyncAction(wrapper, async () => {
    await wrapper.find('[data-testid="load-btn"]').trigger('click');
  });

  expect(wrapper.find('[data-testid="loading"]').exists()).toBe(false);
  expect(wrapper.find('[data-testid="content"]').exists()).toBe(true);
});
```

## RESPONSE FORMAT:

- **WORK SILENTLY** - Do not provide verbose responses for each test addition
- **NO MUNDANE UPDATES** - Don't waste tokens showing test code or "Adding tests for [ComponentName]" messages
- **ONLY RESPOND when milestone is reached:**
  - "Milestone reached: X.X% coverage with all tests passing. Should I commit these changes? (Please verify with your own test run first)"
- **ONLY RESPOND if there are errors/issues** that need human attention

## CURRENT STATUS:

- Current coverage: 98.97%
- Target milestone: 100%
- Framework: Vue 3 + Vitest
- Test runner: Vitest (via Bun)
- Coverage command: `bun run test:js:coverage`

## HUMAN APPROVAL & VERIFICATION PROCESS:

**When Augment asks for commit approval:**

1. Run your own verification:
   ```bash
   bun run test:js           # Verify all tests pass
   bun run test:js:coverage  # Verify coverage reached
   ```
2. Review the test code quality
3. Respond to Augment:
   - **"yes"** - Augment commits automatically
   - **"no"** - Augment should fix issues before asking again
4. After successful commit, update thresholds in vitest.md
5. Tell Augment: "continue"

---

**Remember: QUALITY OVER QUANTITY. ALL TESTS MUST PASS. TEST COMPONENT BEHAVIOR, NOT IMPLEMENTATION DETAILS. ASK FOR APPROVAL BEFORE COMMITTING.**
