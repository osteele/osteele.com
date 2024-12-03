declare module "n3" {
  export class NamedNode implements Term {
    termType: "NamedNode";
    value: string;
    constructor(iri: string);
    equals(other: Term): boolean;
  }

  export interface Prefixes {
    [prefix: string]: string;
  }

  export class Parser {
    constructor(options?: ParserOptions);
    parse(
      input: string,
      callback?: (
        error: Error | null,
        quad: Quad | null,
        prefixes: Prefixes
      ) => void
    ): Quad[];
  }

  export class Store {
    constructor(triples?: Quad[], options?: StoreOptions);
    add(quad: Quad): void;
    addQuad(quad: Quad): void;
    getQuads(
      subject?: Term | string | null,
      predicate?: Term | string | null,
      object?: Term | string | null,
      graph?: Term | string | null
    ): Quad[];
    getSubjects(
      predicate?: Term | string | null,
      object?: Term | string | null,
      graph?: Term | string | null
    ): Term[];
    getPredicates(
      subject?: Term | string | null,
      object?: Term | string | null,
      graph?: Term | string | null
    ): Term[];
    getObjects(
      subject?: Term | string | null,
      predicate?: Term | string | null,
      graph?: Term | string | null
    ): Term[];
  }

  export interface Quad {
    subject: Term;
    predicate: Term;
    object: Term;
    graph?: Term;
  }

  export interface Term {
    termType: string;
    value: string;
    equals(other: Term): boolean;
  }

  export interface ParserOptions {
    format?: string;
    baseIRI?: string;
    blankNodePrefix?: string;
  }

  export interface StoreOptions {
    factory?: {
      namedNode: (value: string) => NamedNode;
      literal: (value: string) => Term;
      defaultGraph: () => Term;
    };
  }

  export function namedNode(value: string): NamedNode;
}
