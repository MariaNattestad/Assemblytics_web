library(ggplot2)


args<-commandArgs(TRUE)
prefix <- args[1]


# TESTING:
# prefix <- "/Applications/XAMPP/htdocs/Assemblytics/tests/Arabidopsis_thaliana_MHAP_assembly"
   
    


filename <- paste(prefix,".coords.flipped",sep="")

ref.pos <- function(chrom,pos,chr.lengths) {
    
    chrom.index <- which(names(chr.lengths)==chrom)-1
    offset.based.on.previous.chromosomes <- 0
    if (chrom.index != 0) {
        offset.based.on.previous.chromosomes <- sum(as.numeric(chr.lengths[c(1:chrom.index)])) 
    }
    
    loc <- offset.based.on.previous.chromosomes + pos   
    loc
}




coords <- read.csv(filename,sep="\t",header=FALSE)

names(coords) <- c("ref.start", "ref.stop","query.start","query.stop","ref.alignment.length","query.alignment.length","percent.identity","ref.length","query.length","ref.fraction.covered","query.fraction.covered","ref","query")
# 
# coords$query <- as.character(coords$query)
# coords$ref <- as.character(coords$ref)

# head(coords)



coords$ref <- as.character(coords$ref)
coords$query <- as.character(coords$query)



ordered_common_chromosome_names <- c(seq(1,100),paste("chr",seq(1,100),sep=""),paste("Chr",seq(1,100),sep=""),c("X","Y","M","MT","Chr0","chr0","0"))

all_chromosomes_some_ordered <- c(intersect(ordered_common_chromosome_names,unique(coords$ref)),setdiff(unique(coords$ref),ordered_common_chromosome_names))



coords$ref <- factor(coords$ref,levels=all_chromosomes_some_ordered)


chromosomes <- levels(coords$ref)

chr.lengths <- sapply(chromosomes,function(chr){max(coords[coords$ref==chr,"ref.length"])})
names(chr.lengths) <- chromosomes

coords <- cbind(coords, alignment.length=abs(coords$query.start-coords$query.stop))


# head(coords)





coords <- cbind(coords, ref.loc.start=mapply(FUN=ref.pos,coords$ref,coords$ref.start,MoreArgs=list(chr.lengths)),
                ref.loc.stop=mapply(FUN=ref.pos,coords$ref,coords$ref.stop,MoreArgs=list(chr.lengths)))

# head(coords)



##### average ref.loc.start for each read.name
##### avg.ref.loc.per.readname <- tapply(filtered.coords$ref.loc.start,factor(filtered.coords$read.name),mean)
########################################

# pick longest alignment. then pick the ref.loc.start of that
query.group <- split(coords,factor(coords$query))

ref.loc.of.longest.alignment.by.query <- unlist(sapply(query.group, function(coords.for.each.query) {coords.for.each.query$ref.loc.start[coords.for.each.query$alignment.length==max(coords.for.each.query$alignment.length)][1]}),recursive=FALSE)


# flip.query <- sapply(query.group,function(coords.for.each.query) {
#     l <- coords.for.each.query[coords.for.each.query$alignment.length==max(coords.for.each.query$alignment.length),][1,]
#     if (l$query.stop<l$query.start) {
#         coords.for.each.query$query.stop <- coords.for.each.query$query.length - coords.for.each.query$query.stop
#         coords.for.each.query$query.start <- coords.for.each.query$query.length - coords.for.each.query$query.start
#     }
#     l$query.stop<l$query.start
# })

#####################################

# query.names <- coords$query

# decide optimal-ish ordering of the queries
ordered.query.names <- names(ref.loc.of.longest.alignment.by.query)[order(ref.loc.of.longest.alignment.by.query)]

# construct a query.lengths list
query.lengths <- sapply(ordered.query.names,function(each.query){
    max(coords[coords$query==each.query,"query.length"])
})

# use the query.lengths to give offset positions to each query, adding a query.loc.start column and a query.loc.stop column to each entry in filtered.coords

coords$query.loc.start <- mapply(FUN=ref.pos,coords$query,coords$query.start,MoreArgs=list(query.lengths))
coords$query.loc.stop <- mapply(FUN=ref.pos,coords$query,coords$query.stop,MoreArgs=list(query.lengths))


#
#
#

# Hide labels for chromosomes accounting for less than 1% of the reference
chr.labels <- names(chr.lengths)
chr.labels[chr.lengths < 0.01*sum(as.numeric(chr.lengths))] <- ""



plot.output.filename <- paste(prefix,".Assemblytics.dotplot.png",sep="")
png(file=plot.output.filename,width=1000,height=1000)




theme_set(theme_bw(base_size = 24))
# 1 line segment for each alignment, linking start and stop
# some kind of gridline on plot to show where contigs start and end
ggplot(coords, aes(x=ref.loc.start,xend=ref.loc.stop,y=query.loc.start,yend=query.loc.stop)) + geom_segment(lineend="butt",size=2) + labs(x="Reference",y="Query") + scale_y_continuous(breaks = cumsum(as.numeric(query.lengths)),labels=NULL,expand=c(0,0)) + scale_x_continuous(breaks = cumsum(as.numeric(chr.lengths)),labels=chr.labels,expand=c(0,0)) + theme(
    axis.ticks.y=element_line(size=0),
    axis.text.x = element_text(angle = 90, hjust = 1,vjust=-0.5),
    panel.grid.major.x = element_line(colour = "black",size=0.2), 
    panel.grid.major.y = element_line(colour = "black",size=0.2), 
#     panel.grid.major.y = element_line(NA),
    panel.grid.minor = element_line(NA)
)



dev.off()









####################################################################################################



##########
# plot.output.filename <- "/Users/mnattest/Dropbox/SKBR3_paper_figures/dotplot_falcon_assembly_colordots.png"
# png(file=plot.output.filename,width=800,height=800)
# ##########
# 
# 
# ggplot(coords,aes(x=ref.loc.start,y=query.loc.start,color=ref)) + geom_point() + labs(x="Reference",y="Contigs")
# 
# ##########
# dev.off()
# ##########
######################################################################
##########


# 
# 
# 
# ################################################################################
# ################################################################################
# ################################################################################
# 
# ##########
# plot.output.filename <- "/Users/mnattest/Dropbox/SKBR3_paper_figures/dotplot_falcon_assembly_linesegments_colorful_1kb_alignments.png"
# png(file=plot.output.filename,width=800,height=800)
# ##########
# 
# #alignment.length.threshold=NULL
# alignment.length.threshold=1000
# 
# p = ggplot(filtered.coords) 
# 
# ############## optional filtering to remove small alignments ##########
# if (!is.null(alignment.length.threshold)) { 
#     
#     align.length.filtered.coords <- filtered.coords[filtered.coords$alignment.length>alignment.length.threshold,]
#     p = ggplot(align.length.filtered.coords) 
# 
# }
# #############################################################################
# 
# 
# p + aes(x=ref.loc.start,xend=ref.loc.stop,y=read.loc.start,yend=read.loc.stop) + geom_segment(lineend="butt", size=10,aes(color=chrom)) + labs(x="Reference",y="Contigs",color="Chromosomes") + scale_y_continuous(breaks = c(0,cumsum(as.numeric(read.lengths))),labels=NULL,expand = c(0,0)) + scale_x_continuous(breaks = c(0,cumsum(as.numeric(chr.lengths))),labels=c("",substr(primary.chr,4,10)[1:10],"","12","","14","","16","","18","","","","","X"),expand = c(0,20000000)) + theme(axis.ticks.y=element_line(size=0),axis.title=element_text(size=20,face="bold"),axis.text=element_text(size=16),legend.text=element_text(size=14),legend.title=element_text(size=20)) + scale_color_manual(values=chrom.col,limits=primary.chr,labels=substr(primary.chr,4,10)) + geom_point(size=1)
# #########
# dev.off()
# #########
