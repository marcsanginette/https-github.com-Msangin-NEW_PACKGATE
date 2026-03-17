import React from 'react';
import { Search, MapPin, ChevronDown, Menu, Heart, ShoppingCart, User, Percent, Star, ChevronRight } from 'lucide-react';

const deals = [
  {
    id: 1,
    category: 'Papel',
    name: 'Caixa de Papelão Ondulado 50x50x50',
    rating: 4.5,
    price: 4.50,
    oldPrice: 6.00,
    image: 'https://images.unsplash.com/photo-1589939705384-5185137a7f0f?w=500&q=80',
    badge: '-25%',
    badgeColor: 'bg-red-500'
  },
  {
    id: 2,
    category: 'Vidro',
    name: 'Pote de Vidro Hermético 1L',
    rating: 5.0,
    price: 12.99,
    image: 'https://images.unsplash.com/photo-1584346133934-a3afd2a33c4c?w=500&q=80',
  },
  {
    id: 3,
    category: 'Plástico',
    name: 'Bobina de Plástico Bolha 100m',
    rating: 4.0,
    price: 45.00,
    image: 'https://images.unsplash.com/photo-1626863905121-3b0c0ed7b94c?w=500&q=80',
    badge: 'Novo',
    badgeColor: 'bg-brand-green'
  },
  {
    id: 4,
    category: 'Metal',
    name: 'Lata de Alumínio para Mantimentos',
    rating: 4.5,
    price: 18.50,
    image: 'https://images.unsplash.com/photo-1614735241165-6756e1df61ab?w=500&q=80',
  },
  {
    id: 5,
    category: 'Madeira',
    name: 'Caixa de Madeira Pinus Decorativa',
    rating: 5.0,
    price: 35.00,
    oldPrice: 40.00,
    image: 'https://images.unsplash.com/photo-1611077544811-042813ce8282?w=500&q=80',
    badge: '-12%',
    badgeColor: 'bg-red-500'
  },
  {
    id: 6,
    category: 'Especiais',
    name: 'Embalagem para Presente Premium',
    rating: 4.5,
    price: 8.99,
    image: 'https://images.unsplash.com/photo-1549465220-1a8b9238cd48?w=500&q=80',
  }
];

const arrivals = [
  {
    id: 7,
    category: 'Sustentável',
    name: 'Sacola Kraft Ecológica (100 un)',
    rating: 4.8,
    price: 89.90,
    image: 'https://images.unsplash.com/photo-1592840062668-9812689c17e6?w=500&q=80',
    badge: 'Novo',
    badgeColor: 'bg-brand-green'
  },
  {
    id: 8,
    category: 'Plástico',
    name: 'Pote Plástico Descartável 250ml (50 un)',
    rating: 4.2,
    price: 15.50,
    image: 'https://images.unsplash.com/photo-1606502973842-f64bc2785fe5?w=500&q=80',
  },
  {
    id: 9,
    category: 'Vidro',
    name: 'Garrafa de Vidro Âmbar 500ml',
    rating: 4.9,
    price: 6.50,
    image: 'https://images.unsplash.com/photo-1622483767028-3f66f32aef97?w=500&q=80',
  },
  {
    id: 10,
    category: 'Metal',
    name: 'Lata de Flandres Redonda',
    rating: 4.5,
    price: 12.00,
    image: 'https://images.unsplash.com/photo-1565586419448-95b774010ee4?w=500&q=80',
  },
  {
    id: 11,
    category: 'Papel',
    name: 'Tubo de Papelão para Envio',
    rating: 4.7,
    price: 3.20,
    oldPrice: 4.00,
    image: 'https://images.unsplash.com/photo-1587582423116-ec07293f0395?w=500&q=80',
    badge: '-20%',
    badgeColor: 'bg-red-500'
  },
  {
    id: 12,
    category: 'Madeira',
    name: 'Palete de Madeira Padrão PBR',
    rating: 4.6,
    price: 45.00,
    image: 'https://images.unsplash.com/photo-1501430654243-c934cec2e1c0?w=500&q=80',
  }
];

const popular = [
  {
    id: 13,
    category: 'Plástico',
    name: 'Saco Plástico Transparente (1000 un)',
    rating: 4.9,
    price: 25.00,
    image: 'https://images.unsplash.com/photo-1530587191325-3db32d826c18?w=500&q=80',
  },
  {
    id: 14,
    category: 'Papel',
    name: 'Caixa para Pizza Oitavada 35cm',
    rating: 4.8,
    price: 2.50,
    image: 'https://images.unsplash.com/photo-1566843972142-a7fcb70de55a?w=500&q=80',
  },
  {
    id: 15,
    category: 'Especiais',
    name: 'Fita Adesiva Personalizada 50m',
    rating: 5.0,
    price: 18.90,
    image: 'https://images.unsplash.com/photo-1586864387789-628af9feed72?w=500&q=80',
    badge: 'Top',
    badgeColor: 'bg-yellow-500'
  },
  {
    id: 16,
    category: 'Vidro',
    name: 'Frasco de Vidro para Perfume 50ml',
    rating: 4.7,
    price: 8.50,
    image: 'https://images.unsplash.com/photo-1594035910387-fea47794261f?w=500&q=80',
  },
  {
    id: 17,
    category: 'Metal',
    name: 'Tambor Metálico 200L',
    rating: 4.5,
    price: 150.00,
    oldPrice: 180.00,
    image: 'https://images.unsplash.com/photo-1605000797499-95a51c5269ae?w=500&q=80',
    badge: '-16%',
    badgeColor: 'bg-red-500'
  },
  {
    id: 18,
    category: 'Sustentável',
    name: 'Embalagem Biodegradável para Hambúrguer',
    rating: 4.9,
    price: 1.20,
    image: 'https://images.unsplash.com/photo-1624372554743-162804798363?w=500&q=80',
  }
];

const TopPromoBar = () => (
  <div className="bg-brand-darkblue text-white text-xs md:text-sm py-2 px-4">
    <div className="container mx-auto flex justify-between items-center">
      <div className="text-center flex-1">
        Aproveite 50% de desconto no seu primeiro pedido. <a className="font-bold underline ml-1" href="#">Compre Agora</a>
      </div>
      <div className="flex gap-4">
        <select className="bg-transparent border-none text-white text-xs focus:ring-0 cursor-pointer outline-none">
          <option className="text-black" value="pt">Português</option>
          <option className="text-black" value="en">English</option>
        </select>
        <select className="bg-transparent border-none text-white text-xs focus:ring-0 cursor-pointer outline-none">
          <option className="text-black" value="brl">BRL</option>
          <option className="text-black" value="usd">USD</option>
        </select>
      </div>
    </div>
  </div>
);

const MainHeader = () => (
  <header className="bg-white py-4 px-4 shadow-sm relative z-20">
    <div className="container mx-auto flex flex-wrap lg:flex-nowrap items-center justify-between gap-4 lg:gap-8">
      <div className="flex-shrink-0">
        <a className="text-2xl font-black text-brand-darkblue tracking-tighter" href="#">PACKGATE</a>
      </div>

      <div className="flex-1 w-full lg:w-auto order-last lg:order-none">
        <form className="flex w-full border border-brand-green rounded-lg overflow-hidden bg-white">
          <select className="hidden md:block bg-gray-50 border-none text-sm text-gray-600 focus:ring-0 cursor-pointer px-4 border-r border-gray-200 outline-none">
            <option>Todas as Categorias</option>
            <option>Plásticas</option>
            <option>Papel</option>
            <option>Madeira</option>
            <option>Metal</option>
            <option>Vidro</option>
            <option>Especiais</option>
          </select>
          <input className="w-full border-none focus:ring-0 text-sm px-4 outline-none" placeholder="Buscar produtos, categorias..." type="text" />
          <button className="bg-brand-green text-white px-6 py-2.5 hover:bg-[#7ab036] transition flex items-center justify-center" type="button">
            <Search className="w-5 h-5" />
          </button>
        </form>
      </div>

      <div className="flex items-center gap-6 flex-shrink-0 text-gray-600">
        <button className="hover:text-brand-green transition relative">
          <User className="w-6 h-6" />
        </button>
        <button className="hover:text-brand-green transition relative">
          <Heart className="w-6 h-6" />
          <span className="absolute -top-1 -right-1 bg-brand-green text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center">0</span>
        </button>
      </div>
    </div>
  </header>
);

const Navigation = () => (
  <nav className="bg-white border-b border-gray-200 relative z-10">
    <div className="container mx-auto px-4 flex items-center gap-8 h-14">
      <div className="h-full flex items-center">
        <button className="bg-brand-green text-white px-5 h-full font-medium flex items-center gap-2 hover:bg-[#7ab036] transition">
          <Menu className="w-5 h-5" />
          Todas as Categorias
        </button>
      </div>
      
      <div className="hidden lg:flex items-center gap-8 text-sm font-medium text-gray-700">
        <a className="text-brand-green" href="#">Início</a>
        <a className="hover:text-brand-green flex items-center gap-1" href="#">Loja <ChevronDown className="w-4 h-4" /></a>
        <a className="hover:text-brand-green" href="#">Sustentáveis</a>
        <a className="hover:text-brand-green" href="#">Personalizadas</a>
        <a className="hover:text-brand-green" href="#">Blog</a>
        <a className="hover:text-brand-green" href="#">Contato</a>
      </div>
    </div>
  </nav>
);

const HeroSection = () => (
  <section className="container mx-auto px-4 py-6">
    <div className="flex flex-col lg:flex-row gap-6">
      <aside className="hidden lg:block w-64 flex-shrink-0 bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
        <ul className="py-2 text-sm text-gray-700">
          {[
            { name: 'Embalagens Plásticas', active: false },
            { name: 'Embalagens de Papel', active: false },
            { name: 'Embalagens de Madeira', active: false },
            { name: 'Embalagens de Metal', active: false },
            { name: 'Embalagens de Vidro', active: true },
            { name: 'Embalagens Especiais', active: false },
            { name: 'Sustentáveis', active: false },
            { name: 'Personalizadas', active: false },
          ].map((cat, idx) => (
            <li key={idx}>
              <a className={`block px-6 py-3 flex items-center gap-3 transition ${cat.active ? 'bg-brand-green text-white' : 'hover:bg-green-50 hover:text-brand-green'}`} href="#">
                <span className={`w-5 h-5 rounded-full inline-block ${cat.active ? 'bg-white/30' : 'bg-gray-200'}`}></span> 
                {cat.name}
              </a>
            </li>
          ))}
          <li>
            <a className="block px-6 py-3 text-center text-brand-green font-medium border-t border-gray-100 mt-2 pt-4" href="#">
              Ver Todas as Categorias
            </a>
          </li>
        </ul>
      </aside>

      <div className="flex-1 bg-gray-100 rounded-2xl relative overflow-hidden min-h-[400px] flex items-center">
        <img alt="Hero Background" className="absolute inset-0 w-full h-full object-cover mix-blend-multiply opacity-40" src="https://images.unsplash.com/photo-1589939705384-5185137a7f0f?q=80&w=2070&auto=format&fit=crop" />
        <div className="relative z-10 p-10 lg:p-16 w-full lg:w-2/3">
          <span className="inline-block px-3 py-1 bg-yellow-400 text-yellow-900 text-xs font-bold rounded-full mb-4">Desconto de Fim de Semana 50%</span>
          <h1 className="text-4xl lg:text-5xl font-bold text-gray-900 leading-tight mb-4">Embalagens de Qualidade Entregues</h1>
          <p className="text-gray-800 font-medium mb-8 text-lg">Compre online e receba as melhores embalagens diretamente na sua empresa.</p>
          <a className="inline-block bg-brand-green text-white px-8 py-3 rounded-full font-medium text-lg hover:bg-[#7ab036] transition shadow-lg shadow-green-200" href="#">Comprar Agora</a>
        </div>
        <img alt="Fresh Produce" className="absolute -right-10 bottom-0 w-2/3 max-w-md hidden md:block object-contain h-[90%]" src="https://images.unsplash.com/photo-1605600659873-d808a13e4d2a?q=80&w=1000&auto=format&fit=crop" style={{ maskImage: 'linear-gradient(to left, rgba(0,0,0,1) 80%, rgba(0,0,0,0))', WebkitMaskImage: 'linear-gradient(to left, rgba(0,0,0,1) 80%, rgba(0,0,0,0))' }} />
      </div>
    </div>
  </section>
);

const ProductCard = ({ product }: { product: any }) => (
  <div className="bg-white border border-gray-100 rounded-xl p-4 relative group hover:shadow-lg hover:border-brand-green transition duration-300 flex flex-col h-full">
    {product.badge && (
      <span className={`absolute top-3 left-3 ${product.badgeColor || 'bg-brand-green'} text-white text-[10px] font-bold px-2 py-0.5 rounded z-10`}>
        {product.badge}
      </span>
    )}
    <button className="absolute top-3 right-3 text-gray-300 hover:text-red-500 z-10 transition">
      <Heart className="w-5 h-5" />
    </button>
    <a className="block relative h-36 mb-3 overflow-hidden rounded-lg bg-gray-50 flex items-center justify-center p-2" href="#">
      <img alt={product.name} className="max-w-full max-h-full object-contain group-hover:scale-110 transition duration-500 rounded" src={product.image} />
    </a>
    <div className="flex-1 flex flex-col">
      <span className="text-xs text-gray-400 mb-1 block">{product.category}</span>
      <a className="font-medium text-gray-800 text-sm leading-tight mb-2 line-clamp-2 hover:text-brand-green" href="#">{product.name}</a>
      <div className="flex items-center mb-2">
        <div className="flex text-yellow-400 text-xs">
          {'★'.repeat(Math.floor(product.rating))}
          {'☆'.repeat(5 - Math.floor(product.rating))}
        </div>
        <span className="text-[10px] text-gray-400 ml-1">({product.rating.toFixed(1)})</span>
      </div>
      <div className="mt-auto flex items-center justify-between mb-3">
        <div className="flex flex-col">
          <span className="font-bold text-lg text-brand-green leading-none">R$ {product.price.toFixed(2).replace('.', ',')}</span>
          {product.oldPrice && (
            <span className="text-xs text-gray-400 line-through mt-0.5">R$ {product.oldPrice.toFixed(2).replace('.', ',')}</span>
          )}
        </div>
      </div>
      <button className="w-full border border-gray-200 text-brand-green font-medium rounded-lg py-1.5 text-sm hover:bg-brand-green hover:text-white hover:border-brand-green transition flex items-center justify-center gap-1">
        Adicionar
      </button>
    </div>
  </div>
);

const ProductSection = ({ title, subtitle, products }: { title: string, subtitle: string, products: any[] }) => (
  <section className="container mx-auto px-4 py-8">
    <div className="flex justify-between items-end mb-6">
      <div>
        <h2 className="text-2xl font-bold text-gray-900">{title}</h2>
        <p className="text-gray-500 text-sm mt-1">{subtitle}</p>
      </div>
      <a className="text-brand-green font-medium hover:underline flex items-center gap-1" href="#">
        Ver tudo <ChevronRight className="w-4 h-4" />
      </a>
    </div>
    <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
      {products.map(product => (
        <ProductCard key={product.id} product={product} />
      ))}
    </div>
  </section>
);

export default function App() {
  return (
    <div className="bg-brand-bg text-gray-800 font-sans antialiased min-h-screen">
      <TopPromoBar />
      <MainHeader />
      <Navigation />
      <main>
        <HeroSection />
        <ProductSection title="Ofertas da Semana" subtitle="Não perca essas ofertas especiais" products={deals} />
        <ProductSection title="Novidades" subtitle="Adicionados recentemente ao nosso catálogo" products={arrivals} />
        <ProductSection title="Mais Populares" subtitle="Os favoritos dos nossos clientes este mês" products={popular} />
      </main>
    </div>
  );
}
