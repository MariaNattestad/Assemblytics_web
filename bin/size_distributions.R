library(ggplot2)


args<-commandArgs(TRUE)
output_prefix <- args[1]

# output_prefix <- "~/Desktop/SIMULATIONS/Assemblytics_results/Human_simulated_insertions"


filename <- paste(output_prefix,".Assemblytics_structural_variants.bed",sep="")

# print(filename)
bed <- read.csv(filename, sep="\t", quote='', header=FALSE)

names(bed)[1:11] <- c("chrom","start","stop","name","size","strand","type","ref.dist","query.dist","contig_position","method.found")
# head(bed)

types.allowed <- c("Insertion","Deletion","Repeat_expansion","Repeat_contraction","Tandem_expansion","Tandem_contraction")


# summary(bed$type)

bed$type <- factor(bed$type, levels = types.allowed)

# summary(bed$type)


# print("Total small variants:")
# print(nrow(bed))


to.file = TRUE

theme_set(theme_gray(base_size = 24))

# head(bed)

# summary(bed$type)

# summary(bed$repeat.class)

# head(bed)


# library(RColorBrewer)
# display.brewer.all()

color_palette_name <- "Set1"
binwidth <- 5



png(paste(output_prefix,".Assemblytics.size_distributions.png", sep=""),1000,1000)
ggplot(bed[bed$size>=50,],aes(x=size, fill=type)) + geom_bar(binwidth=binwidth*10) + scale_fill_brewer(palette=color_palette_name) + facet_grid(type ~ .) + labs(fill="Structural variant type",x="Variant size",y="Count",title="Structural variants > 50 bp") + theme(strip.text=element_blank(),strip.background=element_blank())
dev.off()


png(paste(output_prefix,".Assemblytics.size_distributions_large_structural.png", sep=""),1000,1000)
ggplot(bed[bed$size>=500,],aes(x=size, fill=type)) + geom_bar(binwidth=binwidth*10) + scale_fill_brewer(palette=color_palette_name) + facet_grid(type ~ .) + labs(fill="Structural variant type",x="Variant size",y="Count",title="Structural variants > 500 bp") + theme(strip.text=element_blank(),strip.background=element_blank())
dev.off()





png(paste(output_prefix,".Assemblytics.size_distributions_zoom_structural.png", sep=""),1000,1000)
ggplot(bed[bed$size>=50,],aes(x=size, fill=type)) + geom_bar(binwidth=binwidth) + xlim(0,500) + scale_fill_brewer(palette=color_palette_name) + facet_grid(type ~ .) + labs(fill="Structural variant type",x="Variant size",y="Count",title="Structural variants > 50 bp") + theme(strip.text=element_blank(),strip.background=element_blank())
dev.off()



png(paste(output_prefix,".Assemblytics.size_distributions_zoom.png", sep=""),1000,1000)
ggplot(bed,aes(x=size, fill=type)) + geom_bar(binwidth=binwidth) + scale_fill_brewer(palette=color_palette_name) + xlim(0,500) + facet_grid(type ~ .) + labs(fill="Variant type",x="Variant size",y="Count",title="All variants > 10 bp") + theme(strip.text=element_blank(),strip.background=element_blank())
dev.off()

 
