import Image from "next/image";

interface ProductContent {
  title: string;
  content: string;
  image?: string;
  year?: string;
  role?: string;
}

const productContent: Record<string, ProductContent> = {
  "apple-dylan": {
    title: "Apple Dylan",
    year: "1994-1995",
    role: "Technical Lead",
    content: `Dylan was a programming language based on Common Lisp and Smalltalk. Apple Dylan was an implementation of this language, and an integrated development environment for it. The Dylan programming language and Apple Dylan implementation were developed at Apple Cambridge, originally for the Newton PDA and then for the Macintosh.

Open Dylan is the current implementation of the Dylan programming language.`,
  },
  "nest-learning-thermostat": {
    title: "Nest Learning Thermostat",
    year: "2010-2014",
    role: "Engineering Manager, Technical Operations",
    content: `The Nest Learning Thermostat is a WiFi-connected thermostat with an LCD display, developed mostly by ex-Apple people and using parts from the "smart-phone dividend".

I wrote the initial version of the server software, that mediated remote control (via separately-developed Web, iPhone, and Android apps), collected logs, and delivered OTA updates; then managed the internet services team that replaced this; then managed technical operations that kept this working in the cloud.`,
  },
  "quickdraw-gx": {
    title: "QuickDraw GX",
    year: "1989-1994",
    role: "Software Engineer",
    content: `QuickDraw GX was Apple's next-generation 2D graphics and geometry library for the Macintosh System 7. It featured resolution-independent graphics, sophisticated typography, and a novel object-oriented graphics model.

I implemented the geometry engine, including boolean operations (union, intersection, difference) on arbitrary shapes; and wrote the manual for the geometry portion of the API.`,
  },
  "laszlo-presentation-server": {
    title: "Laszlo Presentation Server",
    year: "2002-2007",
    role: "Chief Software Architect",
    content: `The Laszlo Presentation Server (LPS) was an XML-based platform for developing Rich Internet Applications. It compiled declarative XML application descriptions into Flash applications that ran in the browser.

I was the chief software architect, and implemented the initial compiler and runtime. LPS was later open-sourced as OpenLaszlo.`,
  },
  "pogo-joe": {
    title: "Pogo Joe",
    year: "1983",
    role: "Developer",
    content: `Pogo Joe was a Q*bert-inspired game for the Commodore 64, published by Screenplay. The player bounces around on a pyramid of cubes, changing their colors while avoiding enemies.

This was my first commercial software product, written in 6502 assembly language when I was in high school.`,
  },
  browsegoods: {
    title: "BrowseGoods",
    year: "2007",
    role: "Developer",
    content: `BrowseGoods was a visual browser for Amazon's product catalog. It used novel visualization algorithms to present products in a way that made it easy to compare features and prices.

The application was built using the OpenLaszlo platform and integrated with Amazon's web services.`,
  },
  stylecart: {
    title: "StyleCart",
    year: "2007",
    role: "Developer",
    content: `StyleCart was a visual shopping cart that allowed users to arrange clothing items on a paper doll using drag-and-drop. It was designed to make it easy to create and share outfits.

The application was built using OpenLaszlo and integrated with multiple e-commerce platforms.`,
  },
  "laszlo-webtop-calendar": {
    title: "Laszlo Webtop Calendar",
    year: "2008",
    role: "Architect",
    content: `The Laszlo Webtop Calendar was one of the first web-based calendaring applications with a desktop-like interface. It featured iCalendar/WebDAV integration and real-time updates.

This was a demonstration application for the OpenLaszlo platform, showing how to build complex web applications with rich user interfaces.`,
  },
};

export default async function ProductPage({
  params,
}: {
  params: Promise<{ slug: string }>;
}) {
  const product = productContent[(await params).slug];

  if (!product) {
    return <div>Product not found</div>;
  }

  return (
    <div className="max-w-4xl mx-auto p-8">
      <div className="mb-12">
        <h1 className="text-4xl font-bold mb-4">{product.title}</h1>
        <div className="flex gap-4 text-sm text-gray-600 dark:text-gray-400">
          {product.year && (
            <div className="flex items-center gap-2">
              <span className="font-medium">Year:</span> {product.year}
            </div>
          )}
          {product.role && (
            <div className="flex items-center gap-2">
              <span className="font-medium">Role:</span> {product.role}
            </div>
          )}
        </div>
      </div>

      {product.image && (
        <div className="mb-8">
          <Image
            src={product.image}
            alt={product.title}
            width={500}
            height={300}
          />
        </div>
      )}

      <div className="prose dark:prose-invert max-w-none">
        {product.content.split("\n\n").map((paragraph, index) => (
          <p key={index} className="mb-4 text-lg leading-relaxed">
            {paragraph}
          </p>
        ))}
      </div>
    </div>
  );
}
