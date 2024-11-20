import { WrenchScrewdriverIcon } from "@heroicons/react/24/outline";
import { PageLayout } from "@/components/page-layout";
import Image from "next/image";

interface Project {
  title: string;
  slug: string;
  description?: string;
  imageUrl?: string;
  thumbnailUrl?: string;
}

export default function Woodworking() {
  const projects: Project[] = [
    {
      title: "Shoe Shelves",
      slug: "shoe-shelves",
      imageUrl: "/images/woodworking/shoe-shelves.jpg",
      thumbnailUrl: "/images/woodworking/shoe-shelves-thumb.jpg",
    },
    {
      title: "Honey Locust Shoe Shelf",
      slug: "honey-locust-shoe-shelf",
      imageUrl: "/images/woodworking/honey-locust-shoe-shelf.jpg",
      thumbnailUrl: "/images/woodworking/honey-locust-shoe-shelf-thumb.jpg",
    },
    {
      title: "Plant Stand",
      slug: "plant-stand",
      imageUrl: "/images/woodworking/plant-stand.jpg",
      thumbnailUrl: "/images/woodworking/plant-stand-thumb.jpg",
    },
    {
      title: "Very Narrow Night Table",
      slug: "very-narrow-night-table",
      imageUrl: "/images/woodworking/very-narrow-night-table.jpg",
      thumbnailUrl: "/images/woodworking/very-narrow-night-table-thumb.jpg",
    },
    {
      title: "Network Appliance Cabinet",
      slug: "network-appliance-cabinet",
      imageUrl: "/images/woodworking/network-appliance-cabinet.jpg",
      thumbnailUrl: "/images/woodworking/network-appliance-cabinet-thumb.jpg",
    },
    {
      title: "Scanner Stand",
      slug: "scanner-stand",
      imageUrl: "/images/woodworking/scanner-stand.jpg",
      thumbnailUrl: "/images/woodworking/scanner-stand-thumb.jpg",
    },
    {
      title: "Monitor Stand",
      slug: "monitor-stand",
      imageUrl: "/images/woodworking/monitor-stand.jpg",
      thumbnailUrl: "/images/woodworking/monitor-stand-thumb.jpg",
    },
    {
      title: "Shop Cabinet",
      slug: "shop-cabinet",
      imageUrl: "/images/woodworking/shop-cabinet.jpg",
      thumbnailUrl: "/images/woodworking/shop-cabinet-thumb.jpg",
    },
    {
      title: "Sonos Shelf",
      slug: "sonos-shelf",
      imageUrl: "/images/woodworking/sonos-shelf.jpg",
      thumbnailUrl: "/images/woodworking/sonos-shelf-thumb.jpg",
    },
    {
      title: "Door Brace",
      slug: "door-brace",
      imageUrl: "/images/woodworking/door-brace.jpg",
      thumbnailUrl: "/images/woodworking/door-brace-thumb.jpg",
    },
    {
      title: "Cable Organizer",
      slug: "cable-organizer",
      imageUrl: "/images/woodworking/cable-organizer.jpg",
      thumbnailUrl: "/images/woodworking/cable-organizer-thumb.jpg",
    },
    {
      title: "Tiny Shoe Shelves",
      slug: "tiny-shoe-shelves",
      imageUrl: "/images/woodworking/tiny-shoe-shelves.jpg",
      thumbnailUrl: "/images/woodworking/tiny-shoe-shelves-thumb.jpg",
    },
    {
      title: "(Almost) First Piece",
      slug: "almost-first-piece",
      imageUrl: "/images/woodworking/almost-first-piece.jpg",
      thumbnailUrl: "/images/woodworking/almost-first-piece-thumb.jpg",
    },
    {
      title: "iPad Shim",
      slug: "ipad-shim",
      imageUrl: "/images/woodworking/ipad-shim.jpg",
      thumbnailUrl: "/images/woodworking/ipad-shim-thumb.jpg",
    },
  ];

  return (
    <PageLayout title="Woodworking Projects">
      <div className="w-full">
        <p className="text-xl text-gray-600 dark:text-gray-300 mb-8">
          A collection of handcrafted furniture and wooden objects
        </p>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {projects.map((project) => (
            <div
              key={project.slug}
              className="group relative aspect-square overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-800"
            >
              {project.thumbnailUrl ? (
                <Image
                  src={project.thumbnailUrl}
                  alt={project.title}
                  width={500}
                  height={300}
                  className="h-full w-full object-cover object-center transition-transform duration-300 group-hover:scale-105"
                />
              ) : (
                <div className="flex h-full items-center justify-center">
                  <WrenchScrewdriverIcon className="h-12 w-12 text-gray-400" />
                </div>
              )}
              <div className="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                <div className="absolute bottom-0 left-0 right-0 p-4">
                  <h3 className="text-lg font-semibold text-white">
                    {project.title}
                  </h3>
                  {project.description && (
                    <p className="text-sm text-gray-200">
                      {project.description}
                    </p>
                  )}
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </PageLayout>
  );
}
