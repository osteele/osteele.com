export interface Product {
  title: string;
  description: string;
  href: string;
  year?: string;
  thumbnail?: string;
  role?: string;
  content?: string;
}

export const products: Product[] = [
  {
    title: "Apple Dylan",
    description:
      "A programming language based on Common Lisp and Smalltalk, developed at Apple Cambridge.",
    href: "/products/apple-dylan",
    year: "1994-1995",
    thumbnail: "https://images.osteele.com/products/apple-dylan.webp",
    role: "Technical Lead",
    content: `Dylan was a programming language based on Common Lisp and Smalltalk. Apple Dylan was an implementation of this language, and an integrated development environment for it. The Dylan programming language and Apple Dylan implementation were developed at Apple Cambridge, originally for the Newton PDA and then for the Macintosh.

Open Dylan is the current implementation of the Dylan programming language.`,
  },
  {
    title: "Nest Learning Thermostat",
    description:
      "A WiFi-connected thermostat with an LCD display, developed by ex-Apple people.",
    href: "/products/nest-learning-thermostat",
    year: "2010-2014",
    thumbnail:
      "https://images.osteele.com/products/nest-learning-thermostat.webp",
    role: "Engineering Manager, Technical Operations",
    content: `The Nest Learning Thermostat is a WiFi-connected thermostat with an LCD display, developed mostly by ex-Apple people and using parts from the "smart-phone dividend".

I wrote the initial version of the server software, that mediated remote control (via separately-developed Web, iPhone, and Android apps), collected logs, and delivered OTA updates; then managed the internet services team that replaced this; then managed technical operations that kept this working in the cloud.`,
  },
  {
    title: "QuickDraw GX",
    description:
      "An early-90s 2D graphics and geometry library for the Macintosh System 7.",
    href: "/products/quickdraw-gx",
    year: "1989-1994",
    thumbnail: "https://images.osteele.com/products/quickdraw-gx.webp",
    role: "Software Engineer",
    content: `QuickDraw GX was Apple's next-generation 2D graphics and geometry library for the Macintosh System 7. It featured resolution-independent graphics, sophisticated typography, and a novel object-oriented graphics model.

I implemented the geometry engine, including boolean operations (union, intersection, difference) on arbitrary shapes; and wrote the manual for the geometry portion of the API.`,
  },
  {
    title: "Laszlo Presentation Server",
    description:
      "An XML-based platform for writing Rich Internet Applications (2000-2010).",
    href: "/products/laszlo-presentation-server",
    year: "2002-2007",
    thumbnail:
      "https://images.osteele.com/products/laszlo-presentation-server.webp",
    role: "Chief Software Architect",
    content: `The Laszlo Presentation Server (LPS) was an XML-based platform for developing Rich Internet Applications. It compiled declarative XML application descriptions into Flash applications that ran in the browser.

I was the chief software architect, and implemented the initial compiler and runtime. LPS was later open-sourced as OpenLaszlo.`,
  },
  {
    title: "Pogo Joe",
    description: "A Q*bert-inspired game for the Commodore 64.",
    href: "/products/pogo-joe",
    year: "1983",
    thumbnail: "https://images.osteele.com/products/pogo-joe.webp",
    role: "Developer",
    content: `Pogo Joe was a Q*bert-inspired game for the Commodore 64, published by Screenplay. The player bounces around on a pyramid of cubes, changing their colors while avoiding enemies.

This was my first commercial software product, written in 6502 assembly language when I was in high school.`,
  },
  {
    title: "BrowseGoods",
    description:
      "A visual Amazon catalog browser using novel display algorithms (2007).",
    href: "/products/browsegoods",
    year: "2007",
    thumbnail: "https://images.osteele.com/products/browsegoods.webp",
    role: "Developer",
    content: `BrowseGoods was a visual browser for Amazon's product catalog. It used novel visualization algorithms to present products in a way that made it easy to compare features and prices.

The application was built using the OpenLaszlo platform and integrated with Amazon's web services.`,
  },
  {
    title: "StyleCart",
    description:
      "A visual shopping cart with drag-and-drop paper doll arrangement (2007).",
    href: "/products/stylecart",
    year: "2007",
    thumbnail: "https://images.osteele.com/products/stylecart.webp",
    role: "Developer",
    content: `StyleCart was a visual shopping cart that allowed users to arrange clothing items on a paper doll using drag-and-drop. It was designed to make it easy to create and share outfits.

The application was built using OpenLaszlo and integrated with multiple e-commerce platforms.`,
  },
  {
    title: "Laszlo Webtop Calendar",
    description:
      "An early web-based calendar with iCalendar/WebDAV integration (2008).",
    href: "/products/laszlo-webtop-calendar",
    year: "2008",
    thumbnail:
      "https://images.osteele.com/products/laszlo-webtop-calendar.avif",
    role: "Architect",
    content: `The Laszlo Webtop Calendar was one of the first web-based calendaring applications with a desktop-like interface. It featured iCalendar/WebDAV integration and real-time updates.

This was a demonstration application for the OpenLaszlo platform, showing how to build complex web applications with rich user interfaces.`,
  },
];

export const getProductBySlug = (slug: string): Product | undefined => {
  return products.find((product) => product.href === `/products/${slug}`);
};

export const getAllProducts = (): Product[] => {
  return products;
};
