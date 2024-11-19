import { getProductBySlug } from "@/data/products";
import Image from "next/image";
import { getAllProducts } from "@/data/products";

type Props = {
  params: Promise<{ slug: string }>;
};

export default async function ProductPage({ params }: Props) {
  const { slug } = await params;
  const product = getProductBySlug(slug);

  if (!product) {
    return <div>Product not found</div>;
  }

  if (!product.thumbnail) {
    return <div>Product image not found</div>;
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

      <div className="mb-8">
        <div className="max-w-2xl mx-auto border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden shadow-lg">
          <Image
            src={product.thumbnail}
            alt={product.title}
            width={600}
            height={400}
            className="w-full object-contain bg-gray-50 dark:bg-gray-900"
            priority
          />
        </div>
      </div>

      <div className="prose dark:prose-invert max-w-none">
        {product.content?.split("\n\n").map((paragraph, index) => (
          <p key={index} className="mb-4 text-lg leading-relaxed">
            {paragraph}
          </p>
        ))}
      </div>
    </div>
  );
}

export async function generateStaticParams() {
  const products = getAllProducts();
  return products.map((product) => ({
    slug: product.href.split("/").pop(),
  }));
}
