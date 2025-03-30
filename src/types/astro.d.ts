declare module "astro:content" {
	interface Project {
		name: string;
		repo?: string;
		website?: string;
		description: string;
		categories: string[];
		primaryLanguage?: string;
		dateCreated?: string;
		isArchived?: boolean;
	}

	interface Section {
		id: string;
		name: string;
		color: string;
		titleColor: string;
		description: string;
		subsections?: Subsection[];
		categories?: string[];
	}

	interface Subsection {
		name: string;
		categories?: string[];
	}
}

declare module "bun:test" {
	type TestFunction = (name: string, fn: () => void | Promise<void>, timeout?: number) => void;
	type DescribeFunction = (name: string, fn: () => void) => void;

	interface ExpectMatchers<T> {
		toBe(expected: T): void;
		toEqual(expected: T): void;
		toBeGreaterThan(expected: number): void;
		toHaveLength(expected: number): void;
		toBeDefined(): void;
		toBeUndefined(): void;
		toBeNull(): void;
		toContain(expected: any): void;
		toHaveProperty(property: string, value?: any): void;
	}

	interface ExpectNegatedMatchers<T> {
		toBe(expected: T): void;
		toEqual(expected: T): void;
		toContain(expected: any): void;
		toBeNull(): void;
		toHaveProperty(property: string, value?: any): void;
	}

	interface ExpectResult<T> extends ExpectMatchers<T> {
		not: ExpectNegatedMatchers<T>;
	}

	type ExpectFunction = <T>(actual: T) => ExpectResult<T>;
	type MockFunction = <T extends (...args: any[]) => any>(fn: T) => jest.Mock<ReturnType<T>, Parameters<T>>;

	export const describe: DescribeFunction;
	export const test: TestFunction;
	export const expect: ExpectFunction;
	export const beforeEach: (fn: () => void | Promise<void>) => void;
	export const mock: MockFunction;
}
