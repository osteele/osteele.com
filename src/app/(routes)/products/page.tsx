import Link from "next/link";
import { ArrowRightIcon } from "@heroicons/react/24/outline";
import { PageLayout } from "@/components/page-layout";
import Image from "next/image";
import { products } from "@/data/products";

export default function Products() {
  return (
    <PageLayout title="Products">
      <div className="w-full">
        <p className="text-xl text-gray-600 dark:text-gray-300 mb-8">
          Commercial products and applications I&apos;ve worked on over the
          years
        </p>

        <div className="grid gap-6 md:grid-cols-2">
          {products.map((product) => (
            <Link
              key={product.title}
              href={product.href}
              className="group block p-6 rounded-lg border border-gray-200 hover:border-gray-300
                transition-all duration-200 hover:shadow-md bg-white dark:bg-gray-800
                dark:border-gray-700 dark:hover:border-gray-600"
            >
              {product.thumbnail && (
                <div className="mb-4 overflow-hidden rounded-lg h-48">
                  <Image
                    src={product.thumbnail}
                    alt={product.title}
                    width={300}
                    height={200}
                    className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
                  />
                </div>
              )}
              <div className="flex justify-between items-start mb-2">
                <h2
                  className="text-xl font-semibold group-hover:text-blue-600
                  dark:group-hover:text-blue-400 transition-colors"
                >
                  {product.title}
                </h2>
                <ArrowRightIcon
                  className="h-5 w-5 text-gray-400 group-hover:text-blue-600
                  dark:group-hover:text-blue-400 transition-colors"
                />
              </div>
              {product.year && (
                <div className="text-sm text-gray-500 dark:text-gray-400 mb-2">
                  {product.year}
                </div>
              )}
              <p className="text-gray-600 dark:text-gray-300">
                {product.description}
              </p>
            </Link>
          ))}
        </div>
      </div>
    </PageLayout>
  );
}
